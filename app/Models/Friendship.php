<?php

namespace App\Models;
use App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Friendship extends Model
{
    protected $table = 'friendships';
    
    protected $fillable = [
        'user_id',
        'friend_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public static function makeFriends(User $user, User $friend)
    {
        $existingFriendship = self::where('user_id', $user->id)
            ->where('friend_id', $friend->id)
            ->first();
    
        if ($existingFriendship) {
            return;
        }
    
        self::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id,
        ]);
    
        self::create([
            'user_id' => $friend->id,
            'friend_id' => $user->id,
        ]);
    }
    
    public static function removeFriendship(User $user, User $friend)
    {
        self::where('user_id', $user->id)
            ->where('friend_id', $friend->id)
            ->delete();
            
        self::where('user_id', $friend->id)
            ->where('friend_id', $user->id)
            ->delete();
    }    
    
    public function getFriendsIDs($userId)
    {
        // Retrieve all friends for the given user ID
        $friends = $this->where('user_id', $userId)
                        ->get();

        $friendsIDs = array();
        
        foreach($friends as $friend){
            if($userId == $friend->user_id){
                array_push($friendsIDs, $friend->friend_id);
            }
        }
                

        return $friendsIDs;
    }
}
