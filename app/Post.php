<?php

namespace Cafemedia;

use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'title', 'privacy', 'likes', 'views', 'comments', 'timestamp'];
}
