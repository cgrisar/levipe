<?php

namespace Statamic\Addons\Helpers;

class Attributes {

    protected static $attributes = [
        '19' => 2013,
        '20' => 2014,
        '21' => 2015, 
        '22' => '75cl',
        '23' => 'BiB 5L',
        '24' => 'BiB 10L',
        '25' => '37,5cl',
        '27' => 2016,
        '28' => 2012,
        '29' => 2010,
        '30' => 'NV',
        '31' => 2011,
        '32' => 2009,
        '33' => 2008,
        '34' => '150cl',
        '41' => '300cl',
        '47' => 2017,
        '48' => 2007,
        '49' => 2018,
        '50' => 2019
    ];

    public static function all() 
    {
        return static::$attributes;
    }

    public static function isMillesime($attribute)
    {
        return is_numeric($attribute) or ($attribute === "NV");
    }

    public static function attribute($attribute)
    {
        return static::$attributes[$attribute];
    }

}