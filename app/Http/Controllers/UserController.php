<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create (Request $request) {

        $input = $request->all();
        $user = new User();
        $user->name = $input['name'];
        $user->email = $input['emailco'];
        $user->password = Hash::make($input['password']);

        $user->save();

        $token = $user->createToken('acess token');

        return response()->json(['token' => $token->plainTextToken]);
    }

    public function showToken(Request $request) {
        $user = $request->user();

        return response()->json($user->tokens, Response::HTTP_OK);
    }
}
