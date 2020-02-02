<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

// models
use App\User;
use App\Post;
use App\Follow;

class FollowController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /* follow user */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'follow_user' => 'required',
        ]);
        if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }

        Follow::create([ 
            'follow_id' => $request->input('follow_user'),
            'user_id'   => Auth::user()->id
        ]);

        return response()->json([
            'message' => true,
            'user'    => User::profileData(Auth::user()->id),
            'posts'   => Post::where('user_id', Auth::user()->id)->select('id', 'image')->get()
        ], 200);
    }


    /* get list of following or followers */
    public function show($type)
    {
        if($type === "following"){
            $data = Follow::getFollowingList(Auth::user()->id);
            return !empty($data) && count($data) > 0 
                ? response()->json(['message' => $data], 200) 
                : response()->json(['errors' => 'You follow 0 users!'], 400);
        }
        if($type === "followers"){
            $data = Follow::getFollowersList(Auth::user()->id);
            return !empty($data) && count($data) > 0 
                ? response()->json(['message' => $data], 200) 
                : response()->json(['errors' => 'You have 0 followers!'], 400);
        }
    }

    /* unfollow user */
    public function destroy($id)
    {
        if(Follow::where('follow_id', '=', $id)->where('user_id', '=', Auth::user()->id)->delete()){
            return response()->json([
                'message' => true,
                'user'    => User::profileData(Auth::user()->id),
                'posts'   => Post::where('user_id', Auth::user()->id)->select('id', 'image')->get()
            ], 200);
        }
        return response()->json(["errors" => "User not found. Please try again!"], 400);
    }

}