<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// models
use App\User;
use App\Post;

class RegisterController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'login']]);
    }

    // register
    public function register(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'fullname' => 'required|min:3|max:30',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:18'
        ]);
        if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }

        User::create([
            'fullname' => $request->input('fullname'),
            'username' => User::createUsernameFromFullname($request->input('fullname')),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);

        return response()->json(['message' => 'Profile created successfully!'], 201);
    }

    // login
    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:6|max:191'
        ]);
        if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }

        $token = Auth::setTTL(120)->attempt($request->only(['email', 'password']));
        if(!$token){ return response()->json(['errors' => "Email or password don't exist!"], 400); };
        
        if(Auth::user()->role !== "user") {
            Auth::logout(true);
            return response()->json(['errors' => 'You are not authorized.'], 401); 
        }

        return response()->json([
            'token' => $token,
            'user'  => User::profileData(Auth::user()->id),
            'posts' => Post::where('user_id', Auth::user()->id)->select('id', 'image')->get()
        ], 201);
    }

    // logout
    public function logout()
    {
        Auth::logout(true);    
        return response()->json(['logout'  => true], 200);
    }

}
