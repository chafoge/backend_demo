<?php
namespace src\Middleware;

class Middleware_Controller
{
    protected $container;
    protected $is_perm;
    protected $is_auth;



    public function __construct ($container, $is_perm = false, $is_auth = false) {
        $this->container = $container;
        $this->is_perm = $is_perm;
        $this->is_auth = $is_auth;
    }
}