<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\File;

class UserController extends Controller
{
    public function user_exit($req, $res)
    {
        session_unset();
        session_destroy();
        return $res->withRedirect($this->router->pathFor('home'));
    }

    public function user_enter($req, $res)
    {
        return $this->view->render($res, 'user_login.twig', [
            'active' => 'enter',
        ]);
    }

    public function user()
    {
        if(isset($_SESSION['user'])) {
            return User::find($_SESSION['user']);
        }
        else {
            return false;
        }
    }

    public function createUser($email, $name, $password, $image)
    {
        $user = User::create([
            'email'     => $email,
            'name'      => $name,
            'password'  => $password,
            'image'     => $image,
        ]);
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function getUserByName($name)
    {
        return User::where('name', $name)->first();
    }

    public function showUserPage($req, $res, $args)
    {
        $user = User::findOrFail($args['user_id'])->first();
        $files = $user->files()->where('owner', $user->name)->orderBy('created_at', 'DESC')->get();

        return $this->view->render($res, 'user_page.twig', [
            'user'   => User::find($args['user_id'])->first(),
            'files'  => $files,
            'active' => 'user',
            'title'  => 'Мои файлы:',
        ]);
    }

    public function changeEmail($req, $res, $args)
    {
        $email = $req->getParam('new_email');
        $v = $this->validator->validate([
            'Email'             => [$email , 'required|email|uniqueEmail($email)'],
        ]);
        if(!$v->passes()){
            $_SESSION['errors'] = $v->errors();
            return $res->withRedirect($this->router->pathFor('user.page', [
                'user_id'  => $req->getParam('user_id'),
            ]));
        }

        User::where('id', $id)->update(['email' => $email]);

        return $res->withRedirect($this->router->pathFor('user.page', [
                'user_id'  => $id,
        ]));

    }

    public function changeName($req, $res, $args)
    {
        $name = $req->getParam('new_name');
        $id   = $req->getParam('user_id');

        $v = $this->validator->validate([
            'name|Имя' => [$name , 'required|alnumDash|min(3)|max(30)|uniqueName'],
        ]);
        if(!$v->passes()){
            $_SESSION['errors'] = $v->errors();
            return $res->withRedirect($this->router->pathFor('user.page', [
                'user_id'  => $id,
            ]));
        }
        $old = User::where('id', $id)->first()->name;
        User::where('id', $id)->update(['name' => $name]);
        $this->FileController->changeOwner($old, $name);

        return $res->withRedirect($this->router->pathFor('user.page', [
                'user_id'  => $id,
        ]));
    }

    public function changePass($req, $res, $args)
    {
        $pass = $req->getParam('new_pass');
        $id   = $req->getParam('user_id');

        $v = $this->validator->validate([
            'pass|Пароль' => [$pass , 'required|pass_length'],
        ]);
        if(!$v->passes()){
            $_SESSION['errors'] = $v->errors();
            return $res->withRedirect($this->router->pathFor('user.page', [
                'user_id'  => $id,
            ]));
        }

        $pass = password_hash($pass, PASSWORD_DEFAULT);

        User::where('id', $id)->update(['password' => $pass]);

        return $res->withRedirect($this->router->pathFor('user.page', [
                'user_id'  => $id,
        ]));
    }
}
