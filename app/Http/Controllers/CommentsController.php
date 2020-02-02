<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

// models
use App\Post;
use App\Comment;

// events
use App\Events\NotificationEvent; 

class CommentsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /* create a comment for post */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'comment' => 'required|min:3|max:199',
            'post_id' => 'required',
        ]);
        if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }

        Comment::create([ 
            'post_id' => $request->input('post_id'),
            'user_id' => Auth::user()->id,
            'comment' => $request->input('comment')
        ]);

        event(new NotificationEvent(Post::find($request->input('post_id')), "comment"));

        return response()->json([
            'post'     => Post::getPostById(Auth::user()->id, $request->input('post_id')),
            'comments' => Comment::getCommentsByPostId($request->input('post_id'))
        ], 200);
    }

    /* get comments by post id */
    public function show($id) 
    {
        $comments = Comment::getCommentsByPostId($id);
        return !empty($comments) ? response()->json($comments, 200) : response()->json(["errors" => true], 400);
    }

}