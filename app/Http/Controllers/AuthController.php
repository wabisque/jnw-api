<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Resources\DetailedUserResource;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();

            throw_if(
                blank($user) || !Hash::check($request->password, $user->password),
                new AuthenticationException('Inconsistent credentials.')
            );

            $token = $user->createToken("{$request->ip()}|{$request->header('User-Agent')}");

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Logged in successfully.',
                'data' => [
                    'user' => new DetailedUserResource($user),
                    'token' => $token->plainTextToken,
                ],
            ]);
        } catch(Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 200,
                'message' => 'Logged in user retrieved successfully.',
                'data' => [
                    'user' => new DetailedUserResource($request->user()),
                ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $request->user()->currentAccessToken()->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Logged out successfully.',
                'data' => [],
            ]);
        } catch(Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
