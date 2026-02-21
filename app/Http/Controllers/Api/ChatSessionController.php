<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatSessionController extends Controller
{
    /**
     * Create a new guest session and issue a Sanctum token.
     */
    public function store(): JsonResponse
    {
        $uuid = (string) Str::uuid();

        $user = User::create([
            'name' => 'Visitor',
            'email' => "guest-{$uuid}@visitor.pacman.gr",
            'password' => Str::random(32),
            'is_guest' => true,
        ]);

        $token = $user->createToken('chat-widget')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user_id' => $user->id,
        ], 201);
    }

    /**
     * Get current session info.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user_id' => $user->id,
            'is_guest' => $user->is_guest,
        ]);
    }

    /**
     * Revoke the current token.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Session revoked.']);
    }
}
