<?php

namespace App\Controllers;

use App\Models\File;

class UploadController extends Controller
{
    protected $errors = "";

        public function uploadFile($req, $res, $file=null)
        {
            $folder = date("m.d.y");
            if (!file_exists('../public/uploads/'.$folder)) {
                mkdir('../public/uploads/'.$folder, 0777, true);
            }
            $storage = new \Upload\Storage\FileSystem('../public/uploads/'.$folder);

            if ($_FILES['file']) {
                $file = new \Upload\File('file', $storage);
                $v = $this->validator->validate([
                    'Size|Размер'                  => [$file->getSize(), 'required|filesize'],
                    'Filename|Выбор файла'         => [$file->getExtension(), 'required|max(100)'],
                ]);
                if(!$v->passes()){
                    $_SESSION['errors'] = $v->errors();
                    return $res->withRedirect($this->router->pathFor('home'));
                }
            }
            else {
                return $res->withRedirect($this->router->pathFor('home'));
            }

            $dimention = $file->getDimensions()['height'] . 'x' . $file->getDimensions()['width'];

            $original_filename = $file->getNameWithExtension();
            $new_filename = uniqid() . ".txt";
            $file->setName($new_filename);

            $owner = isset($_SESSION['username']) ? $_SESSION['username'] : "Гость";

            $proper_size = $this->UploadController->getProperSize($file->getSize());

            $data = array(
                'name'           => $new_filename,
                'original_name'  => $original_filename,
                'extension'      => $file->getExtension(),
                'mime'           => $file->getMimetype(),
                'size'           => $proper_size,
                'dimention'      => $dimention,
                'owner'          => $owner,
                'folder'         => $folder,
            );

            $new_file = $this->FileController->createFile($data);
            $id = $new_file->id;

            try {
                $file->upload($new_file->name);
            } catch (\Exception $e) {
                //log error
            }

            $this->flash->addMessage('success', 'Файл загружен');
            
            return $res->withRedirect($this->router->pathFor('file.page', [
                'file_id' => $id,
                'user'    => $_SESSION['username'],
            ]));
        }

    public function getProperSize($size)
    {
        var_dump($size);
        $s = $size;
        $i = 0;
        while (($s = round($s / 1024)) > 0) {
            $size = round(($size / 1024), 2);
            $i += 1;
        }
        unset($s);

        $proper_size = "";

        switch ($i) {
            case 1:
                $proper_size = $size . ' Кб';
                break;
            case 2:
                $proper_size = $size . ' Мб';
                break;
            case 3:
                $proper_size = $size . ' Гб';
                break;
            default:
                $proper_size = $size . ' б';
                break;
        }

        return $proper_size;
    }
}
