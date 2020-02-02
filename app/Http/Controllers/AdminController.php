<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;

// models
use App\User;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index() 
    {
        return response()->json([
        	'user' => User::select('id', 'fullname', 'username', 'email', 'city', 'country', 'profile_image', 'dateofbirth', 'role', 'gender', 'created_at')->where('id', Auth::user()->id)->first()
        ], 201);
    }

    public function update(Request $request)
    {
    	$user = User::find(Auth::user()->id);

        $validator = \Validator::make($request->all(), [
            'fullname' => 'min:3|max:191',
            'username' => 'min:3|max:191',
            'email'    => 'email',
            'password' => 'min:6|max:18',
            'city'     => 'min:4|max:191',
            'country'  => 'min:4|max:191',
            'userfile' => 'image|nullable|max:1999',
            'role'     => 'min:4|max:5',
            'gender'   => 'min:4|max:6'
        ]);
        if($validator->fails()){ return response()->json(['errors' => $validator->errors()->all()], 422); }    	
    
		$image = User::uploadProfileImage($request, $user);

		$password = "";
		if(empty($request->input('password'))){
			$password = $user->password;
		}else {
			$password = Hash::make($request->input('password'));
		}
		
        $update = $user->update([
            'fullname'      => $request->input('fullname'),
            'username'      => $request->input('username'),
            'email'         => $request->input('email'),
            'password'      => $password,
            'city'          => $request->input('city'),
            'country'       => $request->input('country'),
            'profile_image' => $image,
            'role'          => $request->input('role'),
            'gender'        => $request->input('gender')
        ]);

        return $update === true 
            ? response()->json([
                'message' => 'Profile updated successfully!',
                'user'    => User::select('id', 'fullname', 'username', 'email', 'city', 'country', 'profile_image', 'dateofbirth', 'role', 'gender', 'created_at')->where('id', Auth::user()->id)->first()
            ], 200)
            : response()->json(["errors" => 'Error. Please try again!'], 400);
    }


}