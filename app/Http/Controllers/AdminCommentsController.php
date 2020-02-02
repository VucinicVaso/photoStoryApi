<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

// models
use App\Comment;

class AdminCommentsController extends Controller {

	public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
    	return response()->json([
    		'comments' => Comment::select('id', 'post_id', 'user_id', 'comment', 'created_at')->with('post')->with('user')->orderBy('id', 'DESC')->get()
    	], 200);
    }

    public function destroy($id) 
    {
        if(Comment::where('id', $id)->delete()){
            return response()->json([
                'message'  => 'Comment deleted successfully!',
                'comments' => Comment::select('id', 'post_id', 'user_id', 'created_at')->with('post')->with('user')->orderBy('id', 'DESC')->get()
            ], 200);
        }else {
            return response()->json(["errors" => "Comment not found. Please try again!"], 400);
        }
    }

}