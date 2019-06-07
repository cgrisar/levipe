<?php

namespace Statamic\Addons\Lefipe;

use Statamic\Extend\Controller;

class LefipeController extends Controller
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
