<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $req)
    {
        // validate inputs, if any error return errors
        $validation = $this->validateInput($req->all());
        if($validation->count()){
            return response()->json($validation, 400);
        }

        // check if mail exists, if not return error
        if(!User::query()->where(['email' => $req->email])->first()){
            return response()->json(['error' => 'EMAIL_DOES_NOT_EXIST'],400);
        }

        // attempt to login
        $loginAttempt = $this->attemptLogin($req->all());
        if(!$loginAttempt){
            return response()->json(['error'=>'AUTH_FAILED'], 400);
        }

        // create token
        $token = $this->createApiToken($req->token_name);
        if(!$token){
            return response()->json(['error'=>'TOKEN_CREATER_FAILED'], 400);
        }

        return response()->json(['token' => $token], 200);
    }

    private function validateInput ($input) {
        // validate all inputs
        $validator = Validator::make($input, [
            'email' => ['required','email'],
            'password' => ['required'],
            'token_name' => ['required'],
        ]);

        // check if there were any errors
        if($validator->errors()){
            return $validator->errors();
        }

        // return null if there were no errors
        return null;
    }

    public function attemptLogin($input)
    {
        $attempt = Auth::attempt(['email' => $input['email'], 'password' => $input['password']]);

        // returns true if authenticated
        return $attempt;
    }

    public function createApiToken($token_name)
    {
        $token = auth()->user()->createToken($token_name);
        if($token){
            return $token->plainTextToken;
        }
    }
}
