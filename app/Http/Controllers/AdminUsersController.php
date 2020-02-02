<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

// models
use App\User;

class AdminUsersController extends Controller {

	public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
    	return response()->json([
    		'users' => User::select('id', 'fullname', 'profile_image', 'created_at')->where('role', '!=', 'admin')->orderBy('id', 'DESC')->get()
    	], 200);
    }

    public function show($id) 
    {
        return response()->json([
            'user' => User::select('id', 'fullname', 'username', 'email', 'city', 'country', 'profile_image', 'dateofbirth', 'role', 'gender', 'created_at')->where('id', $id)->withCount('posts')->withCount('comments')->with('posts')->with('commentsWithPost')->first()
        ], 200);
    }

    public function destroy($id) 
    {
        if(User::where('id', $id)->delete()){
            return response()->json([
                'message' => 'User deleted successfully!',
                'users'   => User::select('id', 'fullname', 'profile_image', 'created_at')->where('role', '!=', 'admin')->orderBy('id', 'DESC')->get()
            ], 200);
        }else {
            return response()->json(["errors" => "User not found. Please try again!"], 400);
        }
    }

}