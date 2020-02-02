<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

// models
use App\User;
use App\Post;
use App\Comment;

class PostsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /* get all posts */
    public function posts()
    {
        $posts = Post::select('id', 'image')->orderBy('created_at', 'desc')->get();

        return !empty($posts) && count($posts) > 0 
            ? response()->json(["posts" => $posts], 200) 
            : response()->json(["errors" => '0 new posts!'], 400);         
    }

    /* get liked posts */
    public function liked()
    {
        $likedPosts = Post::getLikedPosts(Auth::user()->id);

        return !empty($likedPosts) && count($likedPosts) > 0 
            ? response()->json(["likedPosts" => $likedPosts], 200) 
            : response()->json(["errors" => 'You have 0 liked posts!'], 400);        
    }

    /* get following users posts */
    public function followingPosts()
    {
        $data = Post::getFollowingPosts(Auth::user()->id);

        return !empty($data) && count($data) > 0 
            ? response()->json(["posts" => $data], 200)
            : response()->json(["errors" => 'You follow 0 users!'], 400);        
    }

    /* create new post */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'image'    => 'required|image|nullable|max:1999',
            'captions' => 'required|min:3|max:199',
        ]);
        if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }

        Post::create([ 
            'user_id' => Auth::user()->id,
            'image'   => Post::uploadImage($request),
            'body'    => $request->input('captions'),
        ]);

        return response()->json([
            'message' => 'Post created successfully!',
            'user'    => User::profileData(Auth::user()->id),
            'posts'   => Post::where('user_id', Auth::user()->id)->select('id', 'image')->get()
        ], 200);        
    }

    /* show post by id */
    public function show($id)
    {
        $data = [
            'post'     => Post::getPostById(Auth::user()->id, $id),
            'comments' => Comment::getCommentsByPostId($id)
        ];
        return !empty($data['post']) ? response()->json($data, 200) : response()->json(["errors" => 'Post not found!'], 400);
    }

}
