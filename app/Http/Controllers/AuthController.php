<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),
        [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        if($validator->fails())
        {
            $returnMessage = "";
            foreach($validator->errors()->toArray() as $key => $errorMessages){
                foreach($errorMessages as $errorMessage){
                    $returnMessage .= $errorMessage;
                    $returnMessage .= "\n";
                }
        }
        return response()->json([
            'message' => $returnMessage,
        ],401);
    }
    $input = $request->only(['name','email','password']);
    $input['password'] = Hash::make($input['password']);
    $user = User::create($input);

    $token = $user->createToken('appToken')->accessToken;
    return response()->json([
        'token' => $token,
        'user' => $user,
    ],200);
}
public function signin(Request $request)
{
    $email = $request->input("email");
    $password = $request->input("password");

    if(Auth::attempt(["email"=> $email,"password"=> $password]))
    {
        $user = Auth::user();
        $token = $user->createToken('appToken')->accessToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
        ],200);
    }
    else
    {
        return response()->json([
            'message' => 'Invalid email or password',
        ],401);
    }
}
public function signout(Request $request)
{
    if(Auth::check())
    {
        $token = Auth::user()->token();
        $token->revoke();
        return response()->json([],200);
    }
    else
    {
        return response()->json([
            'message' => 'User not signed in',
        ],401);
    }
}
}