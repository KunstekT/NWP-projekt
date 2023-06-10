<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'content',
        'type_id',
        'user_id',
        'friend_id',
        'type'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
