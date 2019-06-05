<?php

namespace Statamic\Addons\Helpers;

use Statamic\Extend\Tags;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;

class HelpersTags extends Tags
{
    /**
     * The {{ helpers }} tag
     *
     * @return string|array
     */

    private function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }

    public function browserLang()
    {
        echo Request::path();
        echo Request::root();
        echo Request::segment(1);
        
        $site = app()->make('url')->to('/');
        $acceptLang = ['fr', 'nl'];

        $uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        if(in_array($uri[1], $acceptLang))    // the language is valid
        {
            return;
        }

        $lang_browser = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $uri[0] = in_array($lang_browser, $acceptLang) ? $lang_browser : 'fr';
        $uri = '/' . implode('/', $uri);
        throw (new \Statamic\Exceptions\RedirectException)->setUrl($site.$uri);
    }

    /**
     * The {{ helpers:example }} tag
     *
     * @return string|array
     */
    public function example()
    {
        //
    }
}
