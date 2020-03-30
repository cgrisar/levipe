<?php

namespace Statamic\Addons\Odoo;

use Edujugon\Laradoo\Odoo;


class Laradoo {
    public $odoo;

    public function __construct()
    {
        $this->odoo = $this->connectToOdoo();
    }

    protected function connectToOdoo()
    {
        $odoo = new Odoo;
        try {
            $odoo->connect( env('ODOO_DB'), env('ODOO_LOGIN'), env('ODOO_PASSWORD') );
        } catch(Exception $e) {
            echo 'No response';
        };

        return $odoo;

    }
}