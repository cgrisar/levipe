<?php

namespace Statamic\Addons\Laradoo;

use Statamic\Extend\Controller;

class LaradooController extends Controller
{
    /**
     * Maps to your route definition in routes.yaml
     *
     * @return mixed
     */
    public function index()
    {
        return $this->view('index');
    }
}
