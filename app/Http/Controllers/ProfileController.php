<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;

// models
use App\User;
use App\Post;

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

     /* loggedin user's profile data */
    public function index()
    {
        $data = [
            'user'  => User::profileData(Auth::user()->id),
            'posts' => Post::where('user_id', Auth::user()->id)->select('id', 'image')->get()
        ];

        return !empty($data) ? response()->json($data, 200) : response()->json(["errors" => 'User not found!'], 400);
    }

    /* update loggedin user's profile data */
    public function update(Request $request)
    {
        $user = User::find(Auth::user()->id);
        
        if($request->input("type") === "password"){
            $validator = \Validator::make($request->all(), [
                'oldPassword' => ['required', 'min:6', 'max:18', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        return $fail(__('Old password is incorrect.'));
                    }
                }],
                'newPassword' => ['required', 'min:6', 'max:18', function ($attribute, $value, $fail) use ($user) {
                    if (Hash::check($value, $user->password)) {
                        return $fail(__('New password is already taken.'));
                    }
                }],
                'confirmPassword' => 'required|min:6|max:18|same:newPassword',
            ]);
            if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }

            $password = $user->update([ 'password' => Hash::make($request->input('newPassword')) ]);

            return $password === true 
                ? response()->json(['message' => 'Password updated successfully!'], 200)
                : response()->json(["errors" => 'Error. Please try again!'], 400);     
        }

        if($request->input("type") === "profile"){
            $validator = \Validator::make($request->all(), [
                'fullname' => 'min:3|max:191',
                'username' => 'min:3|max:191',
                'email'    => 'email',
                'city'     => 'max:191',
                'country'  => 'max:191',
                'userfile' => 'image|nullable|max:1999',
                'gender'   => 'max:6'
            ]);
            if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }

            $image = User::uploadProfileImage($request, $user);
            $date  = User::convertStringToDate($request->input('date'));

            $update = $user->update([
                'fullname'      => $request->input('fullname'),
                'username'      => $request->input('username'),
                'email'         => $request->input('email'),
                'city'          => $request->input('city'),
                'country'       => $request->input('country'),
                'profile_image' => $image,
                'dateofbirth'   => $date,
                'gender'        => $request->input('gender'),
            ]);

            return $update === true 
                ? response()->json([
                    'message' => 'Profile updated successfully!',
                    'user'    => User::profileData($user->id),
                    'posts'   => Post::where('user_id', $user->id)->select('id', 'image')->get()
                ], 200)
                : response()->json(["errors" => 'Error. Please try again!'], 400);                           
        }
    }

}
