<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;
use DateTime;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'fullname', 'username', 'email', 'password', 'city', 'country', 'profile_image', 'dateofbirth', 'role', 'gender'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'password' ];

    public function getJWTIdentifier() { return $this->getKey(); }

    public function getJWTCustomClaims() { return []; }

    public function posts() { return $this->hasMany('App\Post')->select('id', 'user_id', 'image'); }

    public function comments() { 
        return $this->hasMany('App\Comment')->select('id', 'post_id', 'user_id', 'comment', 'created_at'); 
    }

    public function commentsWithPost() { return $this->comments()->with('post'); }

    public function likes() { return $this->hasMany('App\Like'); }

    public function notifications() { return $this->hasMany('App\Notification'); }

    /* create username from fullname (John Doe => John123) */
    public static function createUsernameFromFullname($fullname) {
        $name     = explode(" ", $fullname);
        $username = $name[0].random_int(100, 1000);
        return $username;
    }

    /* convert string to date format (for photoStoryApp) */
    public static function convertStringToDate($date) {
        $date = new DateTime($date); 
        $date = $date->format("Y-m-d");        
        return $date;
    }

    /* upload profile image (for PhotoStoryApp) */
    public static function uploadProfileImage($request, $user){
        $image = "";
        if($request->hasFile('userfile')) {
            $filenameWithExt = $request->file('userfile')->getClientOriginalName();      /* Get filename with the extension */
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);            /* Get just filename */
            $extension       = $request->file('userfile')->getClientOriginalExtension(); /* Get just extension */
            $fileNameToStore = $filename.'_'.time().'.'.$extension;                      /* Filename to store */
            $destinationPath ="images/";
            $request->file('userfile')->move($destinationPath, $fileNameToStore);        /* Upload Image */
            $image = $fileNameToStore;
        }else {
            $image = $user->profile_image;
        }
        return $image;        
    }

    /* get loggedin user profile data (for PhotoStoryApp) */
    public static function profileData($id) {
        return DB::table('users AS u')
            ->leftJoin('follows AS f1', 'u.id', '=', 'f1.user_id')
            ->leftJoin('follows AS f2', 'u.id', '=', 'f2.follow_id')
            ->leftJoin('posts AS p', 'u.id', '=', 'p.user_id')
            ->where('u.id', $id)
            ->selectRaw('u.id, u.fullname, u.username, u.email, u.city, u.country, u.profile_image, u.dateofbirth, u.gender,
                COUNT(DISTINCT(f1.id)) AS following, COUNT(DISTINCT(f2.id)) AS followers, 
                COUNT(DISTINCT(p.id)) AS count_posts'
            )
            ->first();
    }

    /* user data profile data (for PhotoStoryApp) */
    public static function userData($loggedInUser, $profile) {
        return DB::table('users AS u')
            ->leftJoin('follows AS f1', 'u.id', '=', 'f1.user_id')
            ->leftJoin('follows AS f2', 'u.id', '=', 'f2.follow_id')
            ->leftjoin('follows AS f3', 'u.id','=', DB::raw('f3.follow_id AND f3.user_id = '.$loggedInUser))
            ->leftJoin('posts AS p', 'u.id', '=', 'p.user_id')
            ->where('u.id', $profile)
            ->selectRaw('u.id, u.fullname, u.username, u.profile_image, 
                COUNT(DISTINCT(f1.id)) AS following, COUNT(DISTINCT(f2.id)) AS followers,
                CASE WHEN (f3.id IS NOT NULL) THEN 1 ELSE 0 END AS isFollowed,
                COUNT(DISTINCT(p.id)) AS count_posts'
            )
            ->first();
    }

    /* search for a user by fullname or username (for PhotoStoryApp) */
    public static function search($name) {
        return DB::table('users AS u')
            ->where('u.fullname', 'LIKE', '%'.$name.'%')
            ->orWhere('u.username', 'LIKE', '%'.$name.'%')
            ->selectRaw('u.id, u.fullname, u.profile_image')
            ->get();
    }

}
