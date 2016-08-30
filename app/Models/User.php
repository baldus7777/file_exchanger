<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\File;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
    ];

    public function files()
      {
        return $this->hasMany('App\Models\File', 'owner', 'name');
      }
}
