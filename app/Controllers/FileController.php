<?php

namespace App\Controllers;

use App\Models\File;
use App\Models\Comment;

class FileController extends Controller
{
    public function createFile($data)
    {
        return File::create([
            'name'          => $data['name'],
            'original_name' => $data['original_name'],
            'extension'     => $data['extension'],
            'mime'          => $data['mime'],
            'size'          => $data['size'],
            'dimention'     => $data['dimention'],
            'uploads'       => 0,
            'owner'         => $data['owner'],
            'folder'        => $data['folder'],
        ]);
    }

    public function deleteFile($req, $res, $args)
    {
        $file = File::where('id', $args['file_id'])->first();;
        if (file_exists('../public/uploads/'.$file->folder.'/'.$file->name)) {
                unlink('../public/uploads/'.$file->folder.'/'.$file->name);
        }
        $file->delete();
        $id = $this->UserController->getUserByName($args['user'])->id;
        $this->flash->addMessage('info', 'Файл удален');
        return $res->withRedirect($this->router->pathFor('user.page', [
            'user_id' => $id,
        ]));
    }

    public function watchFile($req, $res, $args)
    {
        $file = File::where('id', $args['file_id'])->first();

        $comments = $file->comments->all();

        $comments_text = array();
        $authors = array();   
        $date = array();           
        $baseUrl = explode("/", $req->getUri())[0] . '//' .
            explode("/", $req->getUri())[2]. "/download/";


        foreach ($comments as $comment) {
          array_push($comments_text,  $comment->comment);
          array_push($authors, $comment->author);
          array_push($date, $comment->created_at);
        }

        $preview = $this->FileController->getPreview($file);

        $player = $this->FileController->getPlayer($file);

        $username = isset($_SESSION['username']) ? $_SESSION['username'] : "";

        return $this->view->render($res, 'file.twig', [
                'file'      => $file,
                'comments'  => $comments_text,
                'authors'   => $authors,
                'date'      => $date,
                'user'      => $username,
                'preview'   => $preview,
                'player'    => $player,
                'baseUrl'   => $baseUrl,
        ]);
    }

    public function getPreview($file)
    {
        switch ($file->extension) {
            case 'jpg':
            case 'png':
                {
                    $name = $file->name . $file->original_name;
                    copy(
                        '../public/uploads/'. $file->folder . '/' . $file->name,
                        '../public/images/previews/'. $name
                        );
                    $_SESSION['preview'] = $name;
                    return "/images/previews/" . $name;
                    break;
                }
                
            case 'wav':
            case 'mp3':
                return "/images/previews/audio_video.png";
                break;
            default:
                return "/images/previews/other.jpg";
                break;
        }
    }

    public function getPlayer($file)
    {
        switch ($file->extension) {   
            case 'wav':
            case 'mp3':
                {
                    return "audio";
                    break;
                }
                break;
            case 'avi':
            case 'mp4':
            case 'wmv':
            case 'webm':
                {
                    return "video";
                    break;
                }
                break;
            default:
                return "";
                break;
        }
    }

    public function getFileById($id)
    {
        return File::findOrFail($id);
    }

    public function get_last_files($per_page)
    {
        return File::orderBy('id', 'desc')->take($per_page)->get();
    }

    public function commentFile($req, $res, $args)
    {
        $author = isset($_SESSION['username']) ? $_SESSION['username'] : "Гость";

        //VALIDATE
        $v = $this->validator->validate([
            'comment|Комментарий'   => [$req->getParsedBody()['comment'], 'required|alNumDashSpace|max(1000)'],
        ]);
        if(!$v->passes()){
            $_SESSION['errors'] = $v->errors();
            return $res->withRedirect($this->router->pathFor('file.page', [
            'file_id' => $args['file_id'],
            ]));
        }    
        $this->CommentController->createComment(
            $args['file_id'],
            $req->getParsedBody()['comment'],
            $author
        );

        return $res->withRedirect($this->router->pathFor('file.page', [
            'file_id' => $args['file_id'],
            'user'    => $_SESSION['username'],
        ])); 
    }

    public function addNote($req, $res, $args)
    {
        $note = $req->getParsedBody()['note'];
        $id   = $args['file_id'];

        //VALIDATE
        $v = $this->validator->validate([
            'note|Примечание'   => [$note, 'required|alNumDashSpace|max(30)'],
        ]);
        if(!$v->passes()){
            $_SESSION['errors'] = $v->errors();
            return $res->withRedirect($this->router->pathFor('file.page', [
                'file_id' => $id,
        ]));
        } 

        File::where('id', $id)->update(['note' => $note]);

        $this->flash->addMessage('info', 'Описание добавлено');

        return $res->withRedirect($this->router->pathFor('file.page', [
            'file_id' => $id,
            'user'    => $_SESSION['username'],
        ])); 
    }

    public function addPass($req, $res, $args)
    {
        $pass = $req->getParsedBody()['file_password'];
        $id   = $args['file_id'];

        //VALIDATE
        $v = $this->validator->validate([
            'pass|Пароль'   => [$pass , 'required|alNumDash|min(3)|max(30)'],
        ]);
        if(!$v->passes()){
            $_SESSION['errors'] = $v->errors();
            return $res->withRedirect($this->router->pathFor('file.page', [
                'file_id' => $id,
        ]));
        } 

        File::where('id', $id)->update(['password' => password_hash($pass, PASSWORD_DEFAULT)]);

        $this->flash->addMessage('info', 'Пароль добавлено');

        return $res->withRedirect($this->router->pathFor('file.page', [
            'file_id' => $id,
            'user'    => $_SESSION['username'],
        ])); 
    }

    public function confirmFile($req, $res, $args)
    {
        $pass = $req->getParsedBody()['file_password'];
        $id   = $args['file_id'];
        $_SESSION['file_conf'] = $id;

        //VALIDATE
        $v = $this->validator->validate([
            'pass|Пароль'   => [$pass , 'required|alNumDash|min(3)|max(30)|file_pass_confirm()'],
        ]);
        if(!$v->passes()){
            $_SESSION['errors'] = $v->errors();
            return $res->withRedirect($this->router->pathFor('file.page', [
                'file_id' => $id,
                'user'    => $_SESSION['username'],
            ])); 
        } 

        // Return link to the file
        $this->DownloadController->downloadFile($req, $res, $args);
    }

    public function search($req, $res, $args)
    {
        $str = $req->getQueryParams()['query'];
        $query = '%' . $str . '%';

        $v = $this->validator->validate([
            'query|Запрос' => [$str, 'required|alnumDash|max(100)'],
        ]);
        if(!$v->passes()){
            $_SESSION['errors'] = $v->errors();
            return $res->withRedirect($this->router->pathFor('home.reg'));
        }
        
        $files = File::where('original_name', 'LIKE', $query)->orderBy('created_at', 'DESC')->get();

        return $this->view->render($res, 'home.twig', [
            'files'  => $files,
            'active' => '',
            'title'  => 'Поиск по строке: '. $str . ' (результат '. count($files) .')' ,
        ]);
    }

    public function changeOwner($old, $owner)
    {
        File::where('owner', $old)->update(['owner' => $owner]);
    }
}
