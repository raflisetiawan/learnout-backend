<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SignInController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'success'   => false,
                'message' => ['Email atau Password yang anda masukkan salah']
            ], 404);
        }

        $token = $user->createToken('ApiToken')->plainTextToken;

        $response = [
            'success'   => true,
            'user'      => $user,
            'token'     => $token
        ];

        return response($response, 201);
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'success'    => true
        ], 200);
    }
}
