<?php

namespace App\Http\Controllers;

use App\Events\MessageSentEvent;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all messages.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Message::with('user')->get(), 200);
    }

    /**
     * Store user's message in database.
     *
     * @param  Request $request
     * @return array
     */
    public function store(Request $request): array
    {
        $user = Auth::user();

        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);

        // send event to listeners
        broadcast(new MessageSentEvent($message, $user))->toOthers();

        return [
            'message' => $message,
            'user' => $user,
        ];
    }
}
