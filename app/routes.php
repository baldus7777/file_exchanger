<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

// Общие маршруты
$app->group('', function() use ($app) {
    $app->get('/', 'HomeController:index')->setName('home');
    $app->post('/', 'UploadController:uploadFile');
    $app->get('/download/{file_id}', 'DownloadController:downloadFile')->setName('download');
    $app->get('/file_page/{file_id}', 'FileController:watchFile')->setName('file.page');
    $app->post('/file_page/{file_id}', 'FileController:commentFile')->setName('file.comment');
    $app->post('/file_confirm/{file_id}', 'FileController:confirmFile')->setName('file.confirm_pass');
    $app->get('/search', 'FileController:search')->setName('search');
});

// Маршруты для незарегистрированных пользователей
$app->group('', function() use ($app) {
    $app->get('/enter', 'UserController:user_enter')->setName('enter');
    $app->post('/enter', 'RegisterController:user_login');
    $app->get('/reg', 'RegisterController:getRegForm')->setName('user.reg');
    $app->post('/reg', 'RegisterController:postRegForm');
})->add(new AuthMiddleware($container));

// Маршруты для зарегистрированных пользователей
$app->group('', function() use ($app) {
    $app->get('/exit', 'UserController:user_exit')->setName('exit');
    $app->get('/user/{user_id}', 'UserController:showUserPage')->setName('user.page');
    $app->get('/delete_file/{file_id}/{user}', 'FileController:deleteFile')->setName('file.delete');
    $app->post('/add_note/{file_id}', 'FileController:addNote')->setName('file.add_note');
    $app->post('/add_pass/{file_id}', 'FileController:addPass')->setName('file.add_pass');
    $app->post('/change_email', 'UserController:changeEmail')->setName('user.change.email');
    $app->post('/change_name', 'UserController:changeName')->setName('user.change.name');
    $app->post('/change_pass', 'UserController:changePass')->setName('user.change.pass');
})->add(new GuestMiddleware($container));
