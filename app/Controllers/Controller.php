<?php

namespace App\Controllers;

class Controller
{
    protected $container;

    public function __construct($c)
    {
        $this->container = $c;
    }

    public function __get($prop)
    {
        if ($this->container->{$prop}) {
            return $this->container->{$prop};
        }
    }
}
