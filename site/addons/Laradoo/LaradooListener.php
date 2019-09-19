<?php

namespace Statamic\Addons\Laradoo;

use Edujugon\Laradoo\Odoo;
use Statamic\Data\Users\User;
use Statamic\Extend\Listener;
use Statamic\Events\Data\UserSaved;

class LaradooListener extends Listener
{
    /**
     * The events to be listened for, and the methods to call.
     *
     * @var array
     */
    public $events = [
        'user.registered' => 'createUserInOdoo'
    ];

    private function connectToOdoo() {

        $odoo = new Odoo;
        try {
            $odoo->connect( env('ODOO_DB'), env('ODOO_LOGIN'), env('ODOO_PASSWORD') );
        } catch(Exception $e) {
            echo 'No response';
        };

        return $odoo;

    }


    public function createUserInOdoo(User $user)
    {
        $odoo = $this->connectToOdoo();

        $ids = $odoo->where('email', '=', $user->get('email'))
                    ->where('name', '=', $user->get('name'))
                    ->search('res.partner');
        
        if(count($ids))
        {
            /**
             * if the registering user exists in Odoo, we'll import its data into Statamic
             */
            $odooUser = $odoo->where('id', '=', $ids->first())
                            ->limit(1)
                            ->fields('street', 'zip', 'city', 'vat', 'lang', 'category_id')
                            ->get('res.partner')
                            ->first();

            $user->set('address', $odooUser['street']);
            $user->set('zip', $odooUser['zip']);
            $user->set('city', $odooUser['city']);
            $user->set('vat', $odooUser['vat']);
            $user->set('odoo_id', $odooUser['id']);
            $user->save();

            return;
        }

        $user->set('odoo_id', $odoo->create('res.partner', [  
                                                'name' => $user->get('name'),
                                                'street' => $user->get('address'),
                                                'zip' => $user->get('zip'),
                                                'city' => $user->get('city'),
                                                'vat' => $user->get('vat'),
                                                'property_account_position_id' => 4,
                                                'email' => $user->get('email'),
                                                'propery_product_pricelist' => 1,
                                                'category_id' => array(array(6, 0, array(42))),
                                                'lang' => app()->getLocale() . '_' . strtoupper(app()->getLocale())
                                                ] 
                                            )
                    );
        $user->save();
    }
}
