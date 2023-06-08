<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class ChatController extends Controller
{ 

    public function index()
    {
        $messages = ChatMessage::orderBy('created_at', 'asc')->get();

        return view('chat', compact('messages'));
    }

    public function chat()
    {            
        $users = User::all();
        $messages = ChatMessage::all();

        $friendshipsController = new FriendshipsController();
        $friendshipsController->refreshFriends(Auth::id());
        $friendshipsController->refreshUsersToAdd(Auth::id());
        return view('chat', ['users' => $users, 'messages' => $messages])->with('receiverId', -1);
    }

    // public function send(Request $request, $receiverId)  
    public function send(Request $request)  
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            // 'sender_id' => 'required',
            'receiver_id' => 'required',
            'message' => 'required',
        ]);
        
        // $sender = $request->input('sender_id');
        $senderId = Auth::id();
        $receiverId = $request->input('receiver_id');

        $sender = User::find($senderId);
        $receiver = User::find($receiverId);

        // Create a new chat message
        $message = ChatMessage::createMessage(
            Auth::id(),
            $validatedData['receiver_id'],
            $validatedData['message']
        );

        // Optionally, you can return a response or redirect to a specific route
        // return response()->json(['message' => 'Message sent successfully', 'data' => $message], 200);
        
        $messages = ChatMessage::where('sender_id', Auth::id())
        ->where('receiver_id', $receiverId)
        ->orWhere(function ($query) use ($receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        $messageListHtml = view('partials.message_list', compact('messages', 'sender', 'receiver'))->render();

        // return redirect()->route('chat', ['sender_id' => $sender, 'receiver_id' => $receiver]);
        return response()->json(['messageListHtml' => $messageListHtml]);
    }

    // public function getMessages($receiverId)
    // {
    //   $messages = ChatMessage::where('sender_id', Auth::id())
    //   ->where('receiver_id', $receiverId)
    //   ->orWhere(function ($query) use ($receiverId) {
    //       $query->where('sender_id', $receiverId)
    //           ->where('receiver_id', Auth::id());
    //   })->orderBy('created_at', 'asc')->get();
  
    //   $messageListHtml = view('partials.message_list', compact('messages', 'receiverId'))->render();
  
    //   return response()->json(['messages' => $messageListHtml]);
    // }

    public function getChatWithAUser($receiverId){
        $users = User::all();
        $messages = ChatMessage::all();
        //$data = ChatMessage::all();// Retrieve the data from the database or other sources
        // $data = ChatMessage::where('sender_id', $senderId)->where('receiver_id', $receiverId)->get();

        $data = ChatMessage::where(function ($query) use ($receiverId) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $receiverId);
        })
        ->orWhere(function ($query) use ($receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', Auth::id());
        })
        ->get();

        $sender = User::find(Auth::id());
        $receiver = User::find($receiverId);                
       
        return view('chat', ['users' => $users, 'messages' => $messages])->with('data', $data)->with('sender', $sender)->with('receiver', $receiver)->with('receiverId', $receiver->id);
    }
}
