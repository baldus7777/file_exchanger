<?php
// DIC configuration

$container = $app->getContainer();

// eloquent set up
try{
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']); 
    $capsule->setAsGlobal();
    $capsule->bootEloquent();    
}
catch(\Illuminate\Database\Exception $e){
    print_r($e);
}
// database connection
$container['db'] = function($c) use ($capsule){
    return $capsule;
};

// controllers dependencies
$container['HomeController'] = function($c){
    return new \App\Controllers\HomeController($c);
};
$container['RegisterController'] = function($c){
    return new \App\Controllers\RegisterController($c);
};
$container['UserController'] = function($c){
    return new \App\Controllers\UserController($c);
};
$container['UploadController'] = function($c){
    return new \App\Controllers\UploadController($c);
};
$container['FileController'] = function($c){
    return new \App\Controllers\FileController($c);
};
$container['DownloadController'] = function($c){
    return new \App\Controllers\DownloadController($c);
};
$container['CommentController'] = function($c){
    return new \App\Controllers\CommentController($c);
};

// slim flash
$container['flash'] = function($c){
    return new \Slim\Flash\Messages;
};

// view renderer
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig($c->get('settings')['renderer']['views_path'],[
        'cache' => false
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $c->router,
        $c->request->getUri()
    ));

    $view->getEnvironment()->addGlobal('auth', [
        'user'      => $c->UserController->user(),
    ]);

    $view->getEnvironment()->addGlobal('flash', $c->flash);
    
    return $view;
};

// violin validator
$container['validator'] = function($c){
    return new \App\Validation\Validator;
};

// csrf guard
$container['csrf'] = function($c){
    return new \Slim\Csrf\Guard;
};

//functions
$container['check_user'] = function($c){
    return isset($_SESSION['user']);
};

