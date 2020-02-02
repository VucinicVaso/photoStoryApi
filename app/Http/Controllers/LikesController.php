<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

// models
use App\Post;
use App\Like;
use App\Comment;

// events
use App\Events\NotificationEvent; 

class LikesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /* like post */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'post_id' => 'required',
        ]);
        if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }

        Like::create([ 
            'post_id' => $request->input('post_id'),
            'user_id' => Auth::user()->id
        ]);

        event(new NotificationEvent(Post::find($request->input('post_id')), "like"));

        return response()->json([ 'likedPosts' => Post::getLikedPosts(Auth::user()->id)], 200);   
    }

    /* unlike post */
    public function destroy($id)
    {
        if(Like::where('post_id', '=', $id)->where('user_id', '=', Auth::user()->id)->delete()){
            return response()->json([ 'likedPosts' => Post::getLikedPosts(Auth::user()->id) ], 200);
        }
        return response()->json(["errors" => ["Post not found. Please try again!"]], 400);
    }

}