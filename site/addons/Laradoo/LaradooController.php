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
