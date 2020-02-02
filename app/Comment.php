<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{

    protected $table = 'comments';

    protected $fillable = [ 'post_id', 'user_id', 'comment'];

    public function user() { return $this->belongsTo('App\User')->select('id', 'fullname', 'profile_image'); }

    public function post() { return $this->belongsTo('App\Post')->select('id', 'image'); }

    /* get comments by post id for Photo Story app */
    public static function getCommentsByPostId($id)
    {
    	return DB::table('comments AS c')
    		->leftJoin('users AS u', 'c.user_id', '=', 'u.id')
    		->where('c.post_id', $id)
    		->selectRaw('c.id, c.comment, u.id AS userId, u.fullname')
    		->get();
    }

}
