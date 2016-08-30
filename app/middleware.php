<?php
// Application middleware

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\RemovePreviewMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));
