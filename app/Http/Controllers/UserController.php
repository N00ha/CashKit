<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
       $fields = $request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|confirmed',
            'tc'=>'required',
        ]);
        if(User::where('email', $request->email)->first())
        {
            return response([
                'message' => 'Email already exists',
                'status'=>'failed'
            ], 200);
        }

        $user = User::create([
            'name'=> $fields['name'],
            'email'=> $fields['email'],
            'password'=>Hash::make($fields['password']),
            'tc'=>json_decode($fields['tc']),
        ]);
        $token = $user->createToken('myapptoken')->plainTextToken;
         $response = [
            'user'=>$user,
            'token'=>$token,
            'message' => 'Registration Success',
            'status'=>'success'
         ];
         return response($response, 201);
    }

    public function login(Request $request){
       $fields = $request->validate([
            'email'=>'required|email|string',
            'password'=>'required',
        ]);
        $user = User::where('email', $fields['email'])->first();
        if($user && Hash::check($fields['password'], $user->password)){
            $token = $user->createToken($fields['email'])->plainTextToken;
            return response([
                'token'=>$token,
                'message' => 'Login Success',
                'status'=>'success'
            ], 200);
        }
        return response([
            'message' => 'The Provided Credentials are incorrect',
            'status'=>'failed'
        ], 401);
    }

    public function logout(Request $request)
    {
        // Get bearer token from the request
        $accessToken = $request->bearerToken();

        // Get access token from database
        $token = PersonalAccessToken::findToken($accessToken);
        // Revoke token
        $token->delete();
        return response([
              'message' => 'Logout Success',
              'status'=>'success'
          ], 200);
    }

    public function change_password(Request $request){
        $request->validate([
            'password' => 'required|confirmed',
        ]);
        $logged_user = auth()->user();
        $logged_user->password = Hash::make($request->password);
        $logged_user->save();
        return response([
            'message' => 'Password Changed Successfully',
            'status'=>'success'
        ], 200);
    }
}
