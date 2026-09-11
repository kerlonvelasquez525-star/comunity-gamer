<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function index(): View
    {
        return view('pages.auth.chatbot');
    }

    public function answer(Request $request, ChatbotService $chatbot): JsonResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
        ]);

        return response()->json($chatbot->answer($validated['question']));
    }
}
