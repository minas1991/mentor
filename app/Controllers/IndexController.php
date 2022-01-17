<?php

namespace App\Controllers;

use App\Base\BaseController;

/**
 * IndexController
 */
class IndexController extends BaseController
{
    /**
     * path
     *
     * @var string
     */
    public $path;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->path = 'index';
    }

    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $this->view($this->path);
    }
}
