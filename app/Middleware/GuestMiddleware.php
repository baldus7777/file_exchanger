<?php

namespace App\Middleware;

class GuestMiddleware extends Middleware
{
    public function __invoke($req, $res, $next){
        if (!$this->container->check_user) {
            return $res->withRedirect($this->container->router->pathFor('home'));
        }

        $res = $next($req, $res);
        return $res;
    }
}
