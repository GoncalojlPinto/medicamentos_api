<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Users
 *
 * endpoints for managing users
 */

class UserController extends Controller
{

    /**
     *  Create new User
     *
     * @bodyParam name string required the users name. Example: John Doe
     * @bodyParam email string required the users email. Example: John.Doe@mai.com
     * @bodyParam password string required the users password. Example: John.Doe@mai.com
     *
     * @unauthenticated
     */
    public function create(Request $request){
        $input = $request->all();
        $user = new User();
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = Hash::make($input['password']);

        $user->save();

        $token = $user->createToken("access_token");

        return response()->json(['token' => $token->plainTextToken]);
    }

    public function showToken(Request $request){
        $user = $request->user();

        // foreach ($user->tokens as $token) {
        //     array_push($tokens, [$token->name => $token->plainTextToken]);
        // }

        return response()->json($user->tokens, Response::HTTP_OK);
    }

    public function login(Request $request) {
        $input = $request->all();
        $rules = ['email' => 'required|email', 'password' => 'required'];

        $validator = Validator::make($input, $rules);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), Response::HTTP_BAD_REQUEST]);
        }

        if (!Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            return response()->json(['message' => 'Invalid Credentials']);
        }
    }
}
