<?php

namespace App\Middleware;

class OldInputMiddleware extends Middleware
{
    public function __invoke($req, $res, $next){
        if (isset($_SESSION['old'])) {
            $this->container->view->getEnvironment()->addGlobal('old', $_SESSION['old']);
            unset($_SESSION['old']);
        }

        $res = $next($req, $res);
        return $res;
    }
}
