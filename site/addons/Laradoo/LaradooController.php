<?php

namespace Statamic\Addons\Laradoo;

use Edujugon\Laradoo\Odoo;
use Statamic\Extend\Controller;
use Statamic\Data\Users\User;
use Statamic\API\User as UserAPI;
use Illuminate\Support\Collection;

class LaradooController extends Controller
{
    private $odoo;
    
    public function __construct()
    {
        $this->odoo = $this->connectToOdoo();
    }

    private function connectToOdoo() {

        $odoo = new Odoo;
        try {
            $odoo->connect( 'gec2', 'charles@grisarco.be', 'Ver0N!que' );
        } catch(Exception $e) {
            echo 'No response';
        };

        return $odoo;

    }

    private function createOrderLines($cartLines)
    {
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

        $orderLines = [];
        
        foreach($cartLines as $cartLine) {
            $orderLine=[];
            $orderLine['product_uom'] = 1;
            $orderLine['product_id'] = $cartLine->variantId + 0;
            $orderLine['name'] = $cartLine->wine . ' ' . $cartLine->vintage . ' ' . $cartLine->volume;
            $orderLine['product_uom_qty'] = $cartLine->ordered;
            $orderLine['price_unit'] = str_replace(',', '.', $cartLine->price) + 0;
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

        // update the contact information with the customer info
        $this->odoo->where('id', $customer['odooId'])
                ->update('res.partner',
                    [
                        'street' => $customer['invoiceStreet'], 
                        'zip' => $customer['invoiceZip'],
                        'city' => $customer['invoiceCity'],
                        'vat' => $customer['invoiceVAT'],
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

    private function createOrder($customer, $cart, $token)
    {
        // update the customer / odoo partner data
        $this->updateInvoiceAddress($customer);
        
        // create order
        $values = [
            'currency_id' => 1,
            'date_order' => date("m/d/Y"),
            'payment_term' => 1,
            'partner_id' => $customer['odooId'] + 0,
            'partner_shipping_id' => $this->getDeliveryAddress($customer),
            'order_line' => $this->createOrderLines($cart)
        ];

        $order_id = $this->odoo->create('sale.order', $values);
        $this->odoo->call('sale.order', 'action_confirm', array($order_id));

        /*
        * Validate the picking. Since we invoice only products that are delivered, we first have to create a transfer
        * and then process that transfer. It is the same as clicking on "validate" on the stock picking view
        */
        $picking_id = $this->odoo->where('id', '=', $order_id)
            ->fields('picking_ids')
            ->get('sale.order')
            ->first();
        $immediate_picking_id=$this->odoo->create('stock.immediate.transfer', array('pick_id' => $picking_id['picking_ids'][0]));
        $id = $this->odoo->call('stock.immediate.transfer', 'process', array($immediate_picking_id));

        // Now that we have a confirmed sale.order, we can charge stripe.

        /*
        * Stripe is charged. We can now create an invoice and acknowledge it's payment.
        * Payment needs to be created and then added to the invoice.
        */
        $sale_order_to_invoice_data = array($order_id, array("context"=>array("active_ids"=>$order_id)));
        $invoice_id = $this->odoo->call('sale.order', 'action_invoice_create', $sale_order_to_invoice_data);
        $id = $this->odoo->call_wf('account.invoice', 'invoice_open', $invoice_id->first());
    }


    private function chargeCreditCard()
    {

    }

    private function createInvoice()
    {

    }

    public function postOrder()
    {
        // get the request parameters
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

        $token = json_decode(request('token'));
        $cart = json_decode(request('cartlines'));

        return $this->createOrder($customer, $cart, $token);
    }
}
