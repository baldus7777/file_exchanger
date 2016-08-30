<?php

namespace App\Validation;

use Violin\Violin;
use App\Models\User;
use App\Models\File;

class Validator extends Violin
{
    public $user_pass = null;

    public function __construct(){
        $this->addRuleMessages([
            'required'    => 'Пожалуйста введите {field}',
            'email'       => '{field} имеет неправильный формат',
            'min'         => 'Длина поля должна быть в предеах от 3 до 30 символов',
            'alnumDash'   => '{field} может содержать только буквы, цифры и _',
            'pass_length' => 'Длина пароля должна быть в предеах от 6 до 30 символов',
            'uniqueEmail' => 'Такой email уже существует',
            'uniqueName'  => 'Такоe имя уже существует',
            'auth_user'   => 'Неправильный email или пароль',
            'filesize'    => 'Допустимый размер файла превышен (максимум 2гб).',
            'alNumDashSpace'    => '{field} может содержать только буквы, цифры пробел и _',
            'file_pass_confirm' => 'Неправильный пароль для скачивания',
            'almun'       => '{field} может содержать только буквы и цифры', 
        ]);
    }

    // Custom rule method for checking a unique email in database.
    public function validate_uniqueEmail($input)
    {
        try{
            return User::where('email', $input)->count() === 0;
        }
        catch(\Illuminate\Database\Exception $e){
            print_r($e);
        }
    }

    public function validate_uniqueName($input)
    {
        try{
            return User::where('name', $input)->count() === 0;
        }
        catch(\Illuminate\Database\Exception $e){
            print_r($e);
        }
    }

     public function validate_pass_length($pass)
    {
        return (strlen($pass) < 6 or strlen($pass) > 30) ? false : true;
    }

    public function validate_image($filedata)
    {
        if ($filedata['size'] == 0){
            return true;
        }
        elseif (strlen($filedata['name']) > 100){
            $this->addRuleMessages([
                'image' => 'Длина имени файла фотографии превышена'
            ]);
            return false;
        }
        elseif ($filedata['extension'] !== 'jpg'){
            $this->addRuleMessages([
                'image' => 'Неправильное расширение файла фотографии'
            ]);
            return false;
        }
        elseif ($filedata['size']/1000000 > 15 ){
            $this->addRuleMessages([
                'image' => 'Размер фотографии не может превышать 15мб'
            ]);
            return false;
        }
        return true;
    }

    public function validate_auth_user($value, $input, $args)
    {   
        $user = User::where('email', $input['Email'])->first();

        if (!$user) {
            return false;
        }

        if (password_verify($input["password"], $user->password)) {
            $_SESSION['user'] = $user->id;
            return true;
        }
        return false;
    }

    public function validate_filesize($value, $input, $args)
    {
        return $value < 2140000000 ? true : false;
    }

    public function validate_alNumDashSpace($value, $input, $args)
    {
        return (bool) preg_match('/^[\pL\pM\pN _-]+$/u', $value);
    }

    public function validate_file_pass_confirm($value, $input, $args)
    {   
        $id = $_SESSION['file_conf'];
        unset($_SESSION['file_conf']);

        $file = File::where('id', $id)->first();

        if (password_verify($value, $file->password)) {
            return true;
        }
        return false;
    }
}
