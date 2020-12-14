<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $input = $request->all();
            $input['remember_token'] = Str::random(10);
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            DB::commit();
            return $this->sendResponse($success, 'Register successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Something Error.', $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        Auth::logout();
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            return $this->sendResponse($success, 'Login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Password mismatch']);
        }
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response['message'] = 'You have been successfully logged out!';
        return $this->sendResponse($response, 'You have been successfully logged out!');
    }
}
