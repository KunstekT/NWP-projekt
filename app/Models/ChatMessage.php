<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'message'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public static function createMessage($senderId, $receiverId, $message)
    {
        return self::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $message,
        ]);
    }

    public static function getAllMessages()
    {
        return self::all();
    }

    public function updateMessage($message)
    {
        $this->message = $message;
        return $this->save();
    }

    public function deleteMessage()
    {
        return $this->delete();
    }
}