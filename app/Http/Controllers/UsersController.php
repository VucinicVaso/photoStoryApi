<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

// models
use App\User;
use App\Post;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /* user data */
    public function show($id)
    {
        $data = [
            'user'  => User::userData(Auth::user()->id, $id),
            'posts' => Post::where('user_id', $id)->select('id', 'image')->get()
        ];
        return !empty($data) ? response()->json($data, 200) : response()->json(["errors" => 'User not found!'], 400);
    }

    /* search users by fullname or username */
    public function search($name)
    {
        $data['users'] = User::search($name);
        return !empty($data) ? response()->json($data, 200) : response()->json(["errors" => 'User not found!'], 400);  
    }

}
