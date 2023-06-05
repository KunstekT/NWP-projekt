<?php

namespace App\Http\Controllers;

use App\Models\Auth;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;

class FriendshipsController extends Controller
{
    public function get($userId)
    {
        $this->refreshFriends($userId);

        return view('friends-list', ['friends' => session('friends')]);
    }

    public function refreshFriends($userId){
        session()->forget('friends');
        $friendship = new Friendship();
        $friendsIDs = $friendship->getFriendsIDs($userId);
        $friends = array();
        $users = User::all();
        foreach($users as $user){
            foreach($friendsIDs as $friendID){
                if($friendID == $user->id){
                    array_push($friends, $user);
                }
            }
        }
        session(['friends' => $friends]);  
    }
    public function refreshUsersToAdd($userId){
        session()->forget('usersToAdd');
        $friendsIDs = array(); // array to hold ids of all friends

        $friendsList = Friendship::all();
        $users = User::all();
        $usersToAdd = array(); // array to hold user ids to be available to add 
        foreach($friendsList as $friendship){   
            if($friendship->user_id == $userId){
                array_push($friendsIDs, $friendship->friend_id);
            }
        }

        foreach($users as $user){

            if($user->id == $userId){

            }
            else if(in_array($user->id, $friendsIDs)){

            }else{
                array_push($usersToAdd, $user);
            }
        }
        
        session(['usersToAdd' => $usersToAdd]);  
    }

    public function findFriends($userId)
    {
        // $userId = $request->input('userId'); 
        // logged in user id
        // $friendId = $request->input('friend_id');
       

        $this->refreshUsersToAdd($userId);

        return view('addUsers', ['usersToAdd' => session(('usersToAdd'))]);
    }
    
    public function addFriend($userId, $friendId)
    {
        // $userId = $request->input('user_id');
        // $friendId = $request->input('friend_id');

        $user = User::find($userId);
        $friend = User::find($friendId);

        if (!$user || !$friend) {
            return response()->json(['message' => 'User or friend not found'], 404);
        }

        Friendship::makeFriends($user, $friend);

        $this->refreshFriends($userId);
        $this->refreshUsersToAdd($userId);
        // return response()->json(['message' => 'Friend added successfully'], 200);
        return view('addUsers', ['usersToAdd' => session('usersToAdd')]);
    }

    public function removeFriend($userId, $friendId)
    {
        $user = User::find($userId);
        $friend = User::find($friendId);

        if (!$user || !$friend) {
            return response()->json(['message' => 'User or friend not found'], 404);
        }

        Friendship::removeFriendship($user, $friend);

        $this->refreshFriends($userId);
        $this->refreshUsersToAdd($userId);

        // return response()->json(['message' => 'Friend removed successfully'], 200);
        return view('friends-list', ['friends' => session('friends')]);
    }
}