<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SignupController extends Controller
{

    public function signup(Request $req)
    {
        // validate inputs, if any error return errors
        $validation = $this->validateInput($req->all());
        if($validation->count()){
            return response()->json($validation, 400);
        }

        // check if role exists
        if(!in_array($req->role, User::$roles)){
            return response()->json(['error' => 'ROLE_DOES_NOT_EXIST'], 400);
        }

        // check if mail has already been used by any user
        if(User::query()->where(['email' => $req->email])->first()){
            return response()->json(['error' => 'USER_DOES_EXIST'],400);
        }

        // create user
        User::query()->create([
            'name' => $req->name,
            'role' => $req->role,
            'email' => $req->email,
            'password' => Hash::make($req->password),
        ]);

        $loginController = new LoginController;
        // authenticate user
        if(!$loginController->attemptLogin($req->all())){
            return response()->json(['error'=>'AUTH_FAILED'], 400);
        }

        // create an api token
        $token = $loginController->createApiToken($req->token_name);
        if(!$token){
            return response()->json(['error'=>'TOKEN_CREATER_FAILED'], 400);
        }

        // return the token
        return response()->json(['token' => $token], 200);
    }

    private function validateInput ($input) {
        // validate all inputs
        $validator = Validator::make($input, [
            'name' => ['required'],
            'role' => ['required'],
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
}
