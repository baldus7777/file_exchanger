<?php

namespace App\Controllers;

use App\Models\File;
use App\Models\Comment;
use Slim\Http\Response;
use Slim\Http\Stream;

class DownloadController extends Controller
{
    public function downloadFile($req, $res, $args)
    {
        $file_id = $args['file_id'];
        $file = $this->FileController->getFileById($file_id);
        $tmp_dir = "../public/uploads/$file->name/";
        $path = "../public/uploads/$file->folder/$file->name";
        mkdir($tmp_dir);
        $tmp_path = $tmp_dir . "$file->original_name";
        copy($path, $tmp_path);

        if (file_exists($tmp_path)) {
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename='.basename($tmp_path));
            header('Content-Type: ' . $file->mime);
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($tmp_path));
            readfile($tmp_path);
            unlink($tmp_path);
            rmdir($tmp_dir);
            $file->increment('uploads');
            exit;        
        }
    }
}
