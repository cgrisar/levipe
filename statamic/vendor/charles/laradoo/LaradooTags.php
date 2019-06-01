<?php

namespace Statamic\Addons\Laradoo;

use Statamic\Extend\Tags;
use Statamic\API\Entry;
use Statamic\Data\Entries\EntryCollection;
use Edujugon\Laradoo\Odoo;

class variants {

}

class LaradooTags extends Tags
{

    private function connectToOdoo() {

        $odoo = new Odoo;
        try {
            $odoo->connect( 'gec2', 'charles@grisarco.be', 'Ver0N!que' );
        } catch(Exception $e) {
            echo 'No response';
        };

        return $odoo;

    }


     /**
     * This function returns the position of vintage in an array of vintages
     * Returns false if it's not in the array of vintages
     */

    private function is_millesime_in_array ( $millesime, $vintages ) {

        $find_vintage = array_filter( $vintages, function( $vintage ) use ( $millesime ) {
            return $vintage["millesime"] === $millesime;
        });

        return key( $find_vintage );
    }

    private function calculate_discounts ( $discount_entries, $discount = [], $wine ='', $vintage = '', $volume ='' ) {

        $discount_entries = $discount_entries->filter( function ( $value ) use ( $wine, $vintage, $volume ) {
            return ( $value->get( 'wine' ) == $wine ) && ( $value->get( 'vintage' ) == $vintage ) && ( $value->get( 'volume' ) == $volume );
        });

        foreach( $discount_entries as $discount_entry ) {

            if ( $discount_entry->get( 'discount_type' ) == 'bogof' ) {

                $bo = $discount_entry->get( 'how_many_to_get_some_free' );
                $gof = $discount_entry->get( 'how_many_free' );
                $bogof_discount = $discount_entry->get( 'discount_free_products' );
                $discount_ratio = ( $gof * ( $bogof_discount / 100 ) ) / $bo;

                $old_discount_ratio = empty( $discount ) ? 0 : $discount["discount_ratio"];

                if ( $discount_ratio > $old_discount_ratio ) {
                    $discount = array( "gof" => $gof, 
                                       "bo" => $bo, 
                                       "bogof_discount" => $bogof_discount,
                                       "discount_ratio" => $discount_ratio 
                                );
                }
            }
        }

        return $discount;
    }

    /** 
    * This function returns the discount from which a customer could benefit (if one is foreseen)
    * @return int
    */

    private function get_discount ( $wine, $vintage, $volume ) {
        
        $discount_entries = Entry::whereCollection( 'discounts' );
        
        // find out if there is a discount rule for the wine code
        $filtered_for_date = $discount_entries->filter( function( $value ) use ( $wine ) {

            // 'wine' is a related field to a collection. We need to find the correct Entry
            // $wine_entry = Entry::find( $value->get('wine')[0] );

            // get the date boundaries
            $after_start_date = $value->get('start_date') <= date( "Y-m-d" );
            $before_end_date = date( "Y-m-d" ) <= $value->get('end_date');

            return ( $after_start_date && $before_end_date ); 

        });

        // now that we've filtered for date, let's find out about the wines and their variants
        // we'll define the discounts from a generic to a specific discount
    
        $discount = $this->calculate_discounts( $filtered_for_date );
        $discount = $this->calculate_discounts( $filtered_for_date, $discount, $wine );
        $discount = $this->calculate_discounts( $filtered_for_date, $discount, $wine, $vintage, $volume );

        return $discount;

    }

    /**
     * This function gets an array of variants and prices and transforms it into 
     * an array that better fits our purposes.
     * 
     * structure of the array is
     * [
     *  [ 
     *      'millesime' => value_millesime,
     *      'description' => value_description,
     *      'variants' => [
     *                      'id' => variant_id,
     *                      'volume' => value_volume,
     *                      'quantity' => value_quantity,
     *                      'price' => value_price
     *      ],
     *  ]
     * ]
     *
     * @return array
     */

    private function transform_variants_array ( $variants, $prices, $wine ) {

        $attributes = [];
        $attributes[19] = 2011;
        $attributes[20] = 2014;
        $attributes[21] = 2015; 
        $attributes[22] = '75cl';
        $attributes[25] ='37,5cl';
        $attributes[27] = 2016;
        $attributes[28] = 2012;
        $attributes[29] = 2010;
        $attributes[33] = 2008;
        $attributes[34] = '150cl';
        $attributes[41] = '300cl';
        $attributes[47] = 2017;

        $rvariants = [];

        // we only need the variants that may be sold
        $variants = array_filter( $variants->toArray(), function( $variant ) {
            return $variant["sale_ok"];
        });

        foreach ( $variants as $variant ) {

            $vintage = [];     // create a helper associative array
            $vintage_variant = [];
            
            $first_attribute = $attributes[$variant["attribute_value_ids"][0]];

            $vintage["millesime"] = $attributes[$variant["attribute_value_ids"][ is_numeric( $first_attribute ) ? 0 : 1 ]];
            $vintage_variant["variantId"] = $variant["id"];
            $vintage_variant["quantity"] = $variant["qty_available"];
            $vintage_variant["volume"] = $attributes[$variant["attribute_value_ids"][ is_numeric( $first_attribute ) ? 1 : 0 ]];
            
            // now we'll find the correct price of the variant
            $variant_price = array_filter( $prices->toArray(), function ( $price ) use ( $variant ) {
                return $price["product_id"][0] === $variant["id"];
            } );

            // current() returns the first element of an array 
            $vintage_variant["price"] = number_format( current( $variant_price )["fixed_price"], 2, ',', '.');

            // merge existing discount with the vintage_variant
            $vintage_variant += $this->get_discount( $wine, $vintage["millesime"], $vintage_variant["volume"] );

            if( !$el = $this->is_millesime_in_array( $vintage["millesime"], $rvariants ) ) {
                $rvariants[] = array_merge( $vintage, array( "variants" => $vintage_variant ) );
            } else {
                $rvariants[$el]["variants"][] = $vintage_variant;
            }

        };
        
        // sort according to millesime
        usort( $rvariants, function ( $item1, $item2 ) {
            return $item1["millesime"] <=> $item2["millesime"];
        });

        // tag a "vintage" around the array
        $vintages["vintages"] = $rvariants;

        return $vintages;

    }

    /**
     * The {{ laradoo:variants_of_a_wine }} tag
     *
     * @return string|array
     */

    public function variantsOfAWine()
    {

        $odoo = $this->connectToOdoo();

        $odoowine = $this->getParam('odoowine');
        $customer_mail = $this->getParam('customer');

        // Find out what pricelist the customer is subject to
        // Set pricelist to 1 - default price list - if the customer is not in the ERP-system
        $pricelist = $odoo  ->where( 'customer', true )
                            ->where( 'email', $customer_mail )
                            ->fields( 'property_product_pricelist' )
                            ->limit( 1 )
                            ->get( 'res.partner' );
        $pricelist = isset( $pricelist[0] ) ? $pricelist[0]["property_product_pricelist"][0] : 1;
        
        // get the product templates
        $template = $odoo   ->where( 'default_code', 'like', $odoowine ) 
                            ->where( 'sale_ok', '=', true )
                            ->fields( 'product_variant_ids' )
                            ->limit( 1 )
                            ->get( 'product.template' );

        // get the product prices from the pricelist
        $prices = $odoo->call(  'product.pricelist.item', 
                                'search_read', 
                                array( array( array( 'pricelist_id', '=', $pricelist ), 
                                                array( 'product_id', 'in', $template[0]["product_variant_ids"] ) ) ),
                                array( 'fields' => array( 'product_id', 'fixed_price' ) )
                    );

        // if we have a template
        if ( count( $template ) ) {

            // then get the variant_ids
            $variant_ids = ( $template[0]["product_variant_ids"] );

            // get all the variants from the template product
            $products = $odoo->call('product.product', 'read', 
                            array( $variant_ids ), 
                            array( 'fields' => array( 'sale_ok', 'attribute_value_ids', 'qty_available' ) ) 
                        ) ;

            return $this->transform_variants_array( $products, $prices, $odoowine ) ;
        
        }
    }

    /** ------------------------------------------------- */

    /**
     * The {{ laradoo:create_user }} tag
     *
     * @return string|array
     */

     public function createUser()
     {
        $odoo = $this->connectToOdoo();
        $id = $odoo->create('res.partner',['name' => 'Jonh Odoo']);
     }
}
