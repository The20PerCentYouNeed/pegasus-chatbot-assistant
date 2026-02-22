<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\PacManCustomerSupportAgent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendMessageRequest;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function store(SendMessageRequest $request)
    {
        $user = $request->user();
        $agent = new PacManCustomerSupportAgent;

        $conversation = Conversation::where('user_id', $user->id)
            ->latest('updated_at')
            ->first();

        if ($conversation) {
            return $agent
                ->continue($conversation->id, as: $user)
                ->stream($request->validated('message'));
        }

        return $agent
            ->forUser($user)
            ->stream($request->validated('message'));
    }

    public function index(Request $request): JsonResponse
    {
        $conversation = Conversation::where('user_id', $request->user()->id)
            ->latest('updated_at')
            ->first();

        if (!$conversation) {
            return response()->json([]);
        }

        $messages = ConversationMessage::where('conversation_id', $conversation->id)
            ->orderBy('created_at')
            ->get(['role', 'content']);

        return response()->json($messages);
    }
}
