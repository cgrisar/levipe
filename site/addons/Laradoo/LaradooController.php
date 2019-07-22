<?php

namespace Statamic\Addons\Laradoo;

use Edujugon\Laradoo\Odoo;
use Statamic\Extend\Controller;

class LaradooController extends Controller
{
    /**
     * Maps to your route definition in routes.yaml
     *
     * @return mixed
     */
    private function connectToOdoo() {

        $odoo = new Odoo;
        try {
            $odoo->connect( 'gec2', 'charles@grisarco.be', 'Ver0N!que' );
        } catch(Exception $e) {
            echo 'No response';
        };

        return $odoo;

    }

    private function createOrderLines($odoo, $cart, $order_id)
    {
        /*
        * The cart array needs to be transformed into another array
        * A valid order line needs
        * - productid
        * - name
        * - quantity
        * - price_unit
        */

        $orderLine = [];
        $orderLine['order_id'] = $order_id + 0;
        $orderLine['product_uom'] = 1;
        
        foreach($cart as $cartLine) {
            $orderLine['product_id'] = $cartLine->variantId + 0;
            $orderLine['name'] = $cartLine->wine . ' ' . $cartLine->vintage . ' ' . $cartLine->volume;
            $orderLine['product_uom_qty'] = $cartLine->ordered;
            $orderLine['price_unit'] = str_replace(',', '.', $cartLine->price) + 0;
            $id = $odoo->create('sale.order.line', $orderLine);

            // any free articles?
            if($cartLine->total_gof > 0) {
                $orderLine['product_uom_qty'] = $cartLine->total_gof;
                $orderLine['price_unit'] = 0;
                $id = $odoo->create('sale.order.line', $orderLine);
            }
        }
    }


    private function checkDeliveryAddress($odoo_id, $cart)
    {
        $odoo = $this->connectToOdoo();
        $odooUsers = $odoo->where('id', '=', $odoo_id)
                            ->fields('name', 'street', 'zip', 'city', 'vat', 'type')
                            ->get('res.partner');
        
        // create order
        $values = [
            'currency_id' => 1,
            'date_order' => date("m/d/Y"),
            'payment_term' => 1,
            'partner_id' => $odoo_id + 0,
         //   'order_line' => $orderLines
        ];

        $order_id = $odoo->create('sale.order', $values);
        $this->createOrderLines($odoo, $cart, $order_id);
        $odoo->call('sale.order', 'action_confirm', array($order_id));

        /*
        * Validate the picking. Since we invoice only products that are delivered, we first have to create a transfer
        * and then process that transfer. It is the same as clicking on "validate" on the stock picking view
        */
        $picking_id = $odoo->where('id', '=', $order_id)
            ->fields('picking_ids')
            ->get('sale.order')
            ->first();
        $immediate_picking_id=$odoo->create('stock.immediate.transfer', array('pick_id' => $picking_id['picking_ids'][0]));
        $odoo->call('stock.immediate.transfer', 'process', array($immediate_picking_id));

        // Now that we have a confirmed sale.order, we can charge stripe.

        $sale_order_to_invoice_data = array($order_id, array("context"=>array("active_ids"=>$order_id)));
        $invoice_id = $odoo->call('sale.order', 'action_invoice_create', $sale_order_to_invoice_data);
        $id = $odoo->call_wf('account.invoice', 'invoice_open', $invoice_id->first());
    }


    private function createOrder($cart)
    {

        $this->checkDeliveryAddress('3', $cart);

        return $orderLines;
    }

    private function chargeCreditCard()
    {

    }

    private function createInvoice()
    {

    }

    public function postOrder()
    {
        $cart = json_decode(request('cart'));
        return $this->createOrder($cart);
    }
}
