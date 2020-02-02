<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Follow extends Model
{

    protected $table = 'follows';

    protected $fillable = [ 'id', 'follow_id', 'user_id'];

    /* get list of following users for loggedin user for PhotoStory app */
    public static function getFollowingList($user) {
    	return DB::table('follows as f')
    		->leftJoin('users AS u', 'f.follow_id', '=', 'u.id')
    		->where('f.user_id', $user)
    		->select('u.id', 'u.fullname', 'u.profile_image')
    		->get();
    }

    /* get list of followers for loggedin user for PhotoStory app */
    public static function getFollowersList($user) {
    	return DB::table('follows as f1')
    		->leftJoin('follows AS f2', 'f1.user_id', DB::raw('f2.follow_id AND f2.user_id = '.$user))
    		->leftJoin('users AS u', 'f1.user_id', '=', 'u.id')
    		->where('f1.follow_id', $user)
    		->selectRaw('u.id, u.fullname, u.profile_image, CASE WHEN (f2.id IS NOT NULL) THEN 1 ELSE 0 END AS isFollowing')
    		->get();
    }

}