<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;


class ChatController extends Controller
{ 
    public function index()
    {
        $messages = ChatMessage::orderBy('created_at', 'asc')->get();

        return view('chat', compact('messages'));
    }

    public function send(Request $request)
    {
        $message = new ChatMessage();
        $message->message = $request->input('message');
        $message->save();

        return redirect('/chat');
    }
}
