<?php

namespace App\Controllers;

use App\Models\File;
use App\Models\Comment;

class CommentController extends Controller
{
    public function createComment($file_id, $comment, $author)
    {
        Comment::create([
            'file_id' => $file_id,
            'comment' => $comment,
            'author'  => $author,
        ]);
    }
}
