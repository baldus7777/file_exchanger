<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',
        'original_name',
        'extension',
        'mime',
        'size',
        'dimention',
        'note',
        'uploads',
        'owner',
        'folder',
    ];

    public function comments()
      {
        return $this->hasMany('App\Models\Comment', 'file_id', 'id');
      }
}
