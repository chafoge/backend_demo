<?php
namespace src\Database;

class Database_Controller
{
    protected $container;

    public function __construct ($container) {
        $this->container = $container;
    }
}