<?php

namespace App\Controllers;

use App\Models\User;

class RegisterController extends Controller
{
    public function getRegForm($req, $res)
    {
        return $this->view->render($res, 'user_reg.twig', [
            'active' => 'reg',
        ]);
    }

    public function postRegForm($req, $res)
    {
        // If user upload image
        $storage = new \Upload\Storage\FileSystem(__DIR__ . '/../../public/images/users');
        if ($file = new \Upload\File('image', $storage)) {
            $file->setName($req->getParam('name') . "_" . $file->getMd5());
            $filedata = array(
                'name'       => $file->getNameWithExtension(),
                'extension'  => $file->getExtension(),
                'size'       => $file->getSize(),
            );
        }

        $v = $this->validator->validate([
            'Email'             => [$req->getParam('email'), 'required|email|uniqueEmail($req->getParam(\'email\'))'],
            'name|Имя'          => [$req->getParam('name'), 'required|alnumDash|min(3)|max(30)|uniqueName'],
            'password|Пароль'   => [$req->getParam('password'), 'required|pass_length'],
            'image|Фотография'  => [$filedata, 'image($filedata)'],
        ]);
        if(!$v->passes()){
            $_SESSION['errors'] = $v->errors();
            $_SESSION['old'] = ([
                'email'       => $req->getParam('email'),
                'name'        => $req->getParam('name'),
                'password'    => $req->getParam('password'),
            ]);
            return $res->withRedirect($this->router->pathFor('user.reg'));
        }

        $user = $this->UserController->createUser(
            $req->getParam('email'),
            $req->getParam('name'),
            password_hash($req->getParam('password'), PASSWORD_DEFAULT),
            $file->getNameWithExtension()
        );

        try{
            if ($filedata['size'] > 0) {
                $file->upload();
            }
        }
        catch (\Exception $e) {
            print_r($file->getErrors());
        }

        $_SESSION['user'] = $user->id;

        $this->flash->addMessage('info', 'Регистрация завершена!');

        return $res->withRedirect($this->router->pathFor('home'));
    }

    public function user_login($req, $res)
    {
        $email = $req->getParam('email');
        $pass  = $req->getParam('password');

        $v = $this->validator->validate([
            'Email'             => [$email, 'required|email'],
            'password|Пароль'   => [$pass,  'required|pass_length'],
            'email_pass'        => [$email, 'auth_user'],
        ]);
        if(!$v->passes()){
            $this->flash->addMessage('error', 'Неправильный логин/пароль');
            $_SESSION['errors'] = $v->errors();
            return $res->withRedirect($this->router->pathFor('enter'));
        }

        $user = $this->UserController->getUserByEmail($email);

        $_SESSION['username'] = $user->name;

        $this->flash->addMessage('success', 'Вы вошли на сайт!');

        return $res->withRedirect($this->router->pathFor('user.page', [
            'user_id' => $user->id,
        ]));
    }
}
