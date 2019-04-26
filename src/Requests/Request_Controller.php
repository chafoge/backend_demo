<?php
namespace src\Requests;


class Request_Controller
{
    protected $container;

    public function __construct ($container) {
        $this->container = $container;
    }
}