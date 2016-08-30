<?php

namespace App\Middleware;

class RemovePreviewMiddleware extends Middleware
{
    public function __invoke($req, $res, $next){
        if (isset($_SESSION['preview'])) {
            unlink('../public/images/previews/'. $_SESSION['preview']);
            unset($_SESSION['preview']);    
        }

        $res = $next($req, $res);
        return $res;
    }
}
