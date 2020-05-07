<?php

namespace Statamic\Addons\Odoo;

use Statamic\Addons\Odoo\Laradoo;
use Statamic\Addons\Helpers\Attributes;

class Wine extends Laradoo 
{
    public $odoocode = '';
    public $name = '';
    public $variants = array();
    public $vintage = '';

    public function __construct ( $odoocode ) 
    {
        parent::__construct();
        $this->odoocode = $odoocode;
    }

    public function getVariantsOfAWine() 
    {
        $variant_ids = $this->odoo->where( 'default_code', 'like', $this->odoocode ) 
                            ->where( 'sale_ok', '=', true )
                            ->fields( 'product_variant_ids' )
                            ->limit( 1 )
                            ->get( 'product.template' )[0]["product_variant_ids"];

        $variants = $this->odoo->call('product.product', 'read', 
                        array( $variant_ids ), 
                        array( 'fields' => array( 'sale_ok', 'attribute_value_ids', 'qty_available' ) ) 
                    );
        
        foreach ($variants as $variant) 
        {
            $product = [];

            $first_attribute = Attributes::attribute($variant["attribute_value_ids"][0]);
            $product["id"] = $variant["id"];
            $product["quantity"] = $variant["qty_available"];
            $product["millesime"] = Attributes::attribute($variant["attribute_value_ids"][ Attributes::isMillesime($first_attribute) ? 0 : 1 ]);
            $product["volume"] = Attributes::attribute($variant["attribute_value_ids"][ Attributes::isMillesime($first_attribute) ? 1 : 0 ]);

            $this->variants[] = $product;
        };
    }

    public function getPriceOfAVintage()
    {
        
    }

}
