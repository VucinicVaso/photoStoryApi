<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;

// models
use App\User;

class AdminRegisterController extends Controller {

    public function __construct()
    {
    	$this->middleware('auth:api', ['except' => ['register', 'login']]);
    }

    public function register(Request $request)
    {
    	$validator = \Validator::make($request->all(), [
            'fullname' => 'required|min:3|max:191',
            'city'     => 'required|min:3|max:191',
            'country'  => 'required|min:3|max:191',
            'birthday' => 'required',
            'gender'   => 'required|min:4|max:6',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:191'
        ]);
        if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }

        User::create([
            'fullname'      => $request->input('fullname'),
            'username'      => User::createUsernameFromFullname($request->input('fullname')),
            'email'         => $request->input('email'),
            'password'      => Hash::make($request->input('password')),
            'city'          => $request->input('city'),
            'country'       => $request->input('country'),
            'profile_image' => 'profile.png',
            'dateofbirth'   => $request->input('birthday'),
            'role'          => 'admin',
            'gender'        => $request->input('gender')
        ]);

        return response()->json(['success' => 'Profile created successfully!'], 201);
    }

    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:6|max:191'
        ]);
        if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }

        $token = Auth::setTTL(120)->attempt($request->only(['email', 'password']));
        if(!$token){ return response()->json(['errors' => "Email or password don't exist!"], 400); };

        if(Auth::user()->role !== "admin") {
            Auth::logout(true);
            return response()->json(['errors' => 'You are not authorized.'], 401); 
        }

        return response()->json([
            'token' => $token
        ], 201);
    }

    public function logout(Request $request)
    {
        Auth::logout(true);
        return response()->json(['logout'  => true], 200);
    }

}