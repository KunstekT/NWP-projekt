<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\User;


class ChatController extends Controller
{ 
    public $_senderId = -1;    
    public $_receiverId = -1;    

    public function index()
    {
        $messages = ChatMessage::orderBy('created_at', 'asc')->get();

        return view('chat', compact('messages'));
    }

    public function chat()
    {            
        $users = User::all();
        $messages = ChatMessage::all();
        return view('chat', ['users' => $users, 'messages' => $messages]);
    }

    public function send(Request $request, $senderId, $receiverId)  
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'sender_id' => 'required',
            'receiver_id' => 'required',
            'message' => 'required',
        ]);

        $sender = $request->input('sender_id');
        $receiver = $request->input('receiver_id');

        // Create a new chat message
        $message = ChatMessage::createMessage(
            $validatedData['sender_id'],
            $validatedData['receiver_id'],
            $validatedData['message']
        );

        // Optionally, you can return a response or redirect to a specific route
        // return response()->json(['message' => 'Message sent successfully', 'data' => $message], 200);
        

        // $message = new ChatMessage();
        // $message->message = $request->input('message');
        // $message->save();

        return redirect()->route('getChatWithAUser', ['sender_id' => $sender, 'receiver_id' => $receiver]);

    }

    public function getChatWithAUser($senderId, $receiverId){
        $users = User::all();
        $messages = ChatMessage::all();
        $_senderId = $senderId;
        $_receiverId = $receiverId;
        //$data = ChatMessage::all();// Retrieve the data from the database or other sources
        // $data = ChatMessage::where('sender_id', $senderId)->where('receiver_id', $receiverId)->get();
        $data = ChatMessage::where(function ($query) use ($_senderId, $_receiverId) {
            $query->where('sender_id', $_senderId)
                ->where('receiver_id', $_receiverId);
        })
        ->orWhere(function ($query) use ($_senderId, $_receiverId) {
            $query->where('sender_id', $_receiverId)
                ->where('receiver_id', $_senderId);
        })
        ->get();

        $username = "Undefined";
        $receiverUsername = "Undefined";
        foreach ($users as $user){
            if($user->id == $senderId)  {
                $username = $user->name;
                break; 
            }                   
        }      
        foreach ($users as $user){
            if($user->id == $receiverId)  {
                $receiverUsername = $user->name;
                break; 
            }                   
        }                   
       
        return view('chat', ['username' => $username,'receiverUsername' => $receiverUsername,'users' => $users, 'messages' => $messages])->with('data', $data)->with('_senderId', $_senderId)->with('_receiverId', $_receiverId);
    }
}
