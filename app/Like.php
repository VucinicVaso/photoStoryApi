<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{

	protected $table = 'likes';

    protected $fillable = [
        'post_id', 'user_id'
    ];

    public function user() { return $this->belongsTo('App\User')->select('id', 'fullname'); }

    public function post() { return $this->belongsTo('App\Post'); }

}
