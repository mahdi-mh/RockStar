<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = Auth::user();
        $token = $user?->createToken('Personal Access Token');

        return response()->json([
            'token' => $token?->plainTextToken,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->getData();
        $data['password'] = bcrypt($request->get('password'));

        $user = User::create($data);

        $token = $user?->createToken('Personal Access Token');

        return response()->json([
            'user' => $user->toArray(),
            'token' => $token,
        ], 201);
    }
}
