<?php

namespace Statamic\Addons\Laradoo;

use Exception;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Edujugon\Laradoo\Odoo;
use Illuminate\Http\Request;
use Statamic\Data\Users\User;
use Statamic\Extend\Controller;
use Statamic\API\User as UserAPI;

use Illuminate\Support\Collection;

use Statamic\Addons\Helpers\Attributes;
use Statamic\Addons\Odoo\Wine;

class LaradooController extends Controller
{
    private $odoo;
    
    public function __construct()
    {
        $this->odoo = $this->connectToOdoo();
    }

    private function connectToOdoo()
    {
        $odoo = new Odoo;
        try 
        {
            $odoo->connect( env('ODOO_DB'), env('ODOO_LOGIN'), env('ODOO_PASSWORD') );
        } 
        catch(Exception $e) 
        {
            echo 'No response';
        };

        return $odoo;

    }

    /**
     * The function returns TRUE or FALSE depending on VAT being or not in the European database
     *
     * @return boolean
     */
    public function postcheckVIES()
    {
        $vat = request('vat');

        try 
        {
            $client = new \SoapClient('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');
            $returnVat = $client->checkVat(array('countryCode' => substr($vat,0, 2), 'vatNumber' => substr($vat, 2)));
            return response()->json(['vatValid' => $returnVat->valid]);
        }
        catch(Exception $e)
        {
            return response()->json(['vatValid' => false]);
        }
    }

    /*
    * The cart array needs to be transformed into another array
    * A valid order line needs
    * - productid
    * - name
    * - product unit of measure
    * - quantity
    * - price_unit
    *
    * The order line is then transformed to a (0,0, orderline) and added to an array
    * This techniques allows to create an order with just 1 call to ODOO.
    */
    private function createOrderLines($cartLines)
    {
        $orderLines = [];
        
        foreach($cartLines as $cartLine) {
            $orderLine=[];
            $orderLine['product_uom'] = 1;
            $orderLine['product_id'] = $cartLine->variantId + 0;
            $orderLine['name'] = $cartLine->wine . ' ' . $cartLine->vintage . ' ' . $cartLine->volume;
            $orderLine['product_uom_qty'] = $cartLine->ordered;
            $orderLine['price_unit'] = str_replace(',', '.', $cartLine->price) + 0;@
            $orderLines[] = array(0, 0, $orderLine);

            // any free articles?
            if($cartLine->total_gof > 0) {
                $orderLine['product_uom_qty'] = $cartLine->total_gof;
                $orderLine['price_unit'] = 0;
                $orderLines[] = array(0, 0, $orderLine);
            }
        }

        return $orderLines;
    }


    private function updateInvoiceAddress($customer)
    {

        // update the contact information with the new customer info
        $this->odoo->where('id', $customer['odooId'])
                ->update('res.partner',
                    [
                        'street' => $customer['invoiceStreet'], 
                        'zip' => $customer['invoiceZip'],
                        'city' => $customer['invoiceCity'],
                        'vat' => trimVAT($customer['invoiceVAT']),
                    ]);            

        // update the user information in Statamic
        $user = UserAPI::getCurrent();
        $user->data(
                array_merge($user->data(),
                    [
                        'address' => $customer['invoiceStreet'],
                        'zip' => $customer['invoiceZip'],
                        'city' => $customer['invoiceCity'],
                        'vat' => $customer['invoiceVAT'],
                    ]
                )
            );
        $user->save();

    }


    private function getDeliveryAddress($customer)
    {   

        $odooUser = $this->odoo->where('id', $customer['odooId'])
                            ->fields('name', 'street', 'zip', 'city', 'vat', 'child_ids')
                            ->get('res.partner')
                            ->first();

        // return the id if we have a match between form input and contact address
        // we can't use array_intersect_assoc since $customer keys also contain the type of address
        if( ($customer['deliveryStreet'] == $odooUser['street']) &&
            ($customer['deliveryZip'] == $odooUser['zip']) &&
            ($customer['deliveryCity'] == $odooUser['city']) )
        {
            return $customer['odooId'];
        }

        // we didn't match, so we're looping through the delivery addresses of odooUser to find a match
        $addresses = $this->odoo->call('res.partner', 'read', 
                            array($odooUser['child_ids']), 
                            array('fields' => array('street', 'zip', 'city', 'type')))
                        ->where('type', 'delivery')
                        ->where('street', $customer['deliveryStreet'])
                        ->where('zip', $customer['deliveryZip'])
                        ->where('city', $customer['deliveryCity']);
        if(!$addresses->isEmpty())
        {
            return $addresses->first()['id'];
        }

        // we didn't find a match in the delivery addresses, 
        // so we create a new delivery address and attach it to odooUser
        return $this->odoo->create('res.partner',
                        [
                            'street' => $customer['deliveryStreet'],
                            'zip' => $customer['deliveryZip'],
                            'city' => $customer['deliveryCity'],
                            'phone' => $customer['deliveryPhone'],
                            'parent_id' => $customer['odooId'],
                            'type' => 'delivery'
                        ]
                    );
    }

    
    private function generatePaymentResponse($intent) 
    {
        if ($intent->status == 'succeeded') 
        {
          # The payment didn’t need any additional actions and completed!
          # Handle post-payment fulfillment
          return response()->json([
            "success" => true
          ]);
        };

        # Note that if your API version is before 2019-02-11, 'requires_action'
        # appears as 'requires_source_action'.
        if ($intent->status == 'requires_source_action' && $intent->next_action->type == 'use_stripe_sdk') 
        {
          # Tell the client to handle the action
          return response()->json([
            'requires_source_action' => true,
            'payment_intent_client_secret' => $intent->client_secret
          ]);
        };

        # Invalid status
        http_response_code(500);
        echo json_encode(['error' => 'Invalid PaymentIntent status']);
    }


    public function getWineVariants(Request $request) 
    {
        $groupedProduct = new Wine($request->query()["params"]["productID"]);

        $groupedProduct->getVariantsOfAWine();

        return response()->json($groupedProduct);
    }


    /**
     * chargeCreditCard charges the credit card in a 2-step way
     * Function is in line with SCA and 3D Secure
     *
     * @param [type] $order
     * @return void
     */
    public function postChargeCreditCard()
    {
        $payment_method = request('payment_method');
        $payment_intent = request('payment_intent');
        Stripe::setApiKey(env('STRIPE_SK'));

        try 
        {
            if($payment_method) 
            {
                $intent = PaymentIntent::create([
                    'amount' => intval(request('amount_total') * 100),
                    'currency' => 'eur',
                    'description' => 'Order ' . request('name'),
                    'statement_descriptor' => 'le.vi.pe order ' . request('name'),
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'payment_method' => $payment_method,
                ]);
            }

            if($payment_intent)
            {
                $intent = PaymentIntent::retrieve($payment_intent);
                $intent->confirm();
            }

            return $this->generatePaymentResponse($intent);
        }

        catch(\Stripe\Error\Card $e)
        { 
            $body = $e->getJsonBody();
            return response()->json(['error' => $body['error']]);
        }
        
        catch(\Stripe\Error\Base $e)
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }


    /**
     * We perform all the actions after the paymet got through
     *  - confirm the sales order
     *  - validate the picking
     *  - create the invoice
     *  - reconcile the payment with the invoice
     * 
     * We return a "confirmed" value if all the actions were performed
     */
    public function postConfirmOrder()
    {
        // $order_id = request('order_id');

        // // confirm the sales order
        // $this->odoo->call('sale.order', 'action_confirm', array($order_id));

        // /**
        // * Validate the picking. Since we invoice only products that are delivered, we first have to create a transfer
        // * and then process that transfer. It is the same as clicking on "validate" on the stock picking view
        // */
        // $picking_id = $this->odoo->where('id', '=', $order_id)
        //     ->fields('picking_ids')
        //     ->get('sale.order')
        //     ->first();
        // $immediate_picking_id=$this->odoo->create('stock.immediate.transfer', array('pick_id' => $picking_id['picking_ids'][0]));
        // $this->odoo->call('stock.immediate.transfer', 'process', array($immediate_picking_id));

        // /**
        //  * Create the invoice related to order and picking
        //  */
        // $sale_order_to_invoice_data = array($order_id, array('context' => array('active_ids' => $order_id)));
        // $invoice_id = $this->odoo->call('sale.order', 'action_invoice_create', $sale_order_to_invoice_data);
        // $this->odoo->call_wf('account.invoice', 'invoice_open', $invoice_id->first());

        /**
         * Create the payment in Odoo
         */


        return response()->json(['confirmed' => true]);
    }


    public function postCreateOrder()
    {
        $customer = [
            'odooId' => request('odooId') + 0,

            'deliveryStreet' => request('delAddress'),
            'deliveryZip' => request('delZip'),
            'deliveryCity' => request('delCity'),
            'deliveryPhone' => request('delPhone'),

            'invoiceStreet' => request('address'),
            'invoiceZip' => request('zip'),
            'invoiceCity' => request('city'),
            'invoicePhone' => request('phone'),
            'invoiceVAT' => request('VAT'),
        ];

        $cart = json_decode(request('cartlines'));

        // update the customer / odoo partner data
        $this->updateInvoiceAddress($customer);
        
        // create order
        $values = [
            'currency_id' => 1,
            'date_order' => date("m/d/Y"),
            'payment_term_id' => 1,
            'team_id' => 2,
            'partner_id' => $customer['odooId'] + 0,
            'partner_shipping_id' => $this->getDeliveryAddress($customer),
            'order_line' => $this->createOrderLines($cart)
        ];

        $order_id = $this->odoo->create('sale.order', $values);

        $order = $this->odoo->where('id', $order_id)
                        ->fields('name', 'amount_total')
                        ->get('sale.order')
                        ->first();

        return response()->json($order);
    }

}
