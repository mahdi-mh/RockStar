<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class AuthController extends Controller
{
    /**
     * Login
     *
     * Login user and return JWT token
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @group Auth
     * @unauthenticated
     *
     * @bodyParam email string required Email.
     * @bodyParam password string required Password.
     * @responseFile status=200 scenario="Successfully logged in" storage/example-response/auth/login-200.json
     * @responseFile status=422 scenario="Validation error" storage/example-response/auth/login-422.json
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $token = $user?->createToken('Personal Access Token');

        return response()->json([
            'user' => $user?->toArray(),
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * Register
     *
     * Register user and return JWT token
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @group Auth
     * @unauthenticated
     *
     * @bodyParam name string required Name.
     * @bodyParam email string required Email.
     * @bodyParam password string required Password.
     * @bodyParam password_confirmation string required Password confirmation.
     * @responseFile status=201 scenario="Successfully registered" storage/example-response/auth/register-201.json
     * @responseFile status=422 scenario="Validation error" storage/example-response/auth/register-422.json
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->getData();
        $data['password'] = bcrypt($request->get('password'));

        $user = User::create($data);

        $token = $user?->createToken('Personal Access Token');

        return response()->json([
            'user' => $user->toArray(),
            'token' => $token->plainTextToken,
        ], Response::HTTP_CREATED);
    }
}
