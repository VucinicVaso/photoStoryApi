<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Post extends Model
{

    protected $table = 'posts';

    protected $fillable = [ 'user_id', 'image',  'body'];

    public function user() { return $this->belongsTo('App\User')->select('id', 'fullname'); }

    public function comments() { return $this->hasMany('App\Comment')->select('id', 'post_id', 'user_id', 'comment', 'created_at'); }

    public function commentsWithUser() { return $this->comments()->with('user'); }

    public function likes() { return $this->hasMany('App\Like'); }

    /* upload image for Photo Story app */
    public static function uploadImage($request){
        $image = "";
        if($request->hasFile('image')) {
            $filenameWithExt = $request->file('image')->getClientOriginalName();      /* Get filename with the extension */
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);         /* Get just filename */
            $extension       = $request->file('image')->getClientOriginalExtension(); /* Get just extension */
            $fileNameToStore = $filename.'_'.time().'.'.$extension;                   /* Filename to store */
            $destinationPath ="images/";
            $request->file('image')->move($destinationPath, $fileNameToStore);        /* Upload Image */
            $image = $fileNameToStore;
        }
        return $image;    
    }

    /* get post by id for Photo Story app */
    public static function getPostById($loggedInUser, $postId)
    {
    	return DB::table('posts as p')
    		->leftJoin('users AS u', 'p.user_id', '=', 'u.id')
    		->leftJoin('likes AS l', 'p.id', DB::raw('l.post_id AND l.user_id = '.$loggedInUser))
    		->leftJoin('follows as f', 'p.user_id', DB::raw('f.follow_id AND f.user_id = '.$loggedInUser))
    		->where('p.id', $postId)
    		->selectRaw('p.id, p.image, p.body, p.created_at, u.id as userId, u.fullname, u.profile_image,
    			CASE WHEN (l.id IS NOT NULL) THEN 1 ELSE 0 END AS isLiked,
    			CASE WHEN (f.id IS NOT NULL) THEN 1 ELSE 0 END AS isFollowed
    			')
    		->first();
    }

    /* get liked posts by loggedin user for Photo Story app */
    public static function getLikedPosts($user) 
    {
        return DB::table('posts AS p')
            ->leftJoin('likes AS l', 'p.id', '=', 'l.post_id')
            ->where('l.user_id', $user)
            ->select('p.id', 'p.image')
            ->get();
    }

    /* for Photo Story app */
    public static function getFollowingPosts($user) 
    {
        return DB::table('posts AS p')
            ->leftJoin('follows AS f', 'p.user_id', '=', 'f.follow_id')
            ->leftJoin('users AS u', 'f.follow_id', '=', 'u.id')
            ->leftJoin('likes as l', 'p.id', DB::raw('l.post_id AND l.user_id = '.$user))
            ->where('f.user_id', $user)
            ->orderBy('p.id', 'desc')
            ->selectRaw('p.id, p.image,  u.id AS userId, u.fullname, u.profile_image,
                CASE WHEN (l.id IS NOT NULL) THEN 1 ELSE 0 END AS isLiked')
            ->get();
    }

}
