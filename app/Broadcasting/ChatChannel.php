<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function join($user)
    {
        return $user->id;
    }

    public function chatMessage($user, $receiverId)
    {
        return $user->id === Auth::id() || $receiverId === Auth::id();
    }

    // public function broadcastOn()
    // {
    //     return new Channel('chat');
    // }

    // public function broadcastWith()
    // {
    //     // Customize the data you want to send to the client
    //     return [
    //         'message' => $this->message,
    //         'user_id' => $this->user->id,
    //     ];
    // }
}
