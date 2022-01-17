<?php

namespace App\Base;

/**
 * BaseController for special case
 */
class BaseController
{
    /**
     * currentController
     *
     * @var mixed
     */
    protected $currentController;

    /**
     * currentMethod
     *
     * @var mixed
     */
    protected $currentMethod;

    /**
     * params
     *
     * @var array
     */
    protected $params = array();

    /**
     * __construct
     */
    public function __construct()
    {
        $url = $this->getUrl();

        if (!$url) {
            $this->currentController = 'IndexController';
            $this->currentMethod = 'index';
            //require controller
            require_once './app/Controllers/' . $this->currentController . '.php';
            $this->currentController = '\App\Controllers\\' . $this->currentController;
        } else {
            //Look In Controllers For First Value
            $url[0] = str_replace('Controller', '', $url[0]) . 'Controller';
            if (file_exists('./app/Controllers/' . ucwords($url[0]) . '.php')) {

                //if exists, set as controller
                $this->currentController = ucwords($url[0]);

                //require controller
                require_once './app/Controllers/' . $this->currentController . '.php';
                //unset 0 index
                unset($url[0]);
            } else {
                header("HTTP/1.0 404 Not Found");
                exit;
            }

            //check for the second part of the url
            if (isset($url[1])) {
                $this->currentController = '\App\Controllers\\' . $this->currentController;
                //check to see if method exist
                if (method_exists($this->currentController, $url[1])) {
                    $this->currentMethod = $url[1];

                    //unset 1 index
                    unset($url[1]);
                } else {
                    header("HTTP/1.0 404 Not Found");
                    exit;
                }
            } else {
                $this->currentMethod = 'index';
            }

            // get params
            $this->params = $url ? array_values($url) : [];
        }

        //instantiate controller class
        $this->currentController = new $this->currentController();

        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    /**
     * getUrl
     *
     * @return array|null
     */
    public function getUrl(): ?array
    {
        if (empty($_GET['url'])) {
            return null;
        }

        $url = trim($_GET['url'], '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        return $url;
    }

    /**
     * view
     *
     * @param  mixed $view
     * @return void
     */
    public function view($view)
    {
        if (file_exists(__DIR__ . '/../views/' . $view . '.php')) {
            require_once __DIR__ . '/../views/' . $view . '.php';
        } else {
            //view does not exist
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    }
}
