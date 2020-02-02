<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

// models
use App\Post;

class AdminPostsController extends Controller {

	public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
    	return response()->json([
    		'posts' => Post::select('id', 'user_id', 'image', 'body', 'created_at')->with('user')->orderBy('id', 'DESC')->get()
    	], 200);
    }

    public function show($id) 
    {
        return response()->json([
            'post' => Post::select('id', 'user_id', 'image', 'body', 'created_at')
                        ->where('id', $id)
                        ->with('user')
                        ->withCount('comments')
                        ->with('commentsWithUser')
                        ->first()
        ], 200);
    }

    public function destroy($id)
    {
        if(Post::where('id', $id)->delete()){
            return response()->json([
                'message' => 'Post deleted successfully!',
                'posts'   => Post::select('id', 'user_id', 'image', 'body', 'created_at')->with('user')->orderBy('id', 'DESC')->get()
            ], 200);
        }else {
            return response()->json(["errors" => "Post not found. Please try again!"], 400);
        }
    }

}