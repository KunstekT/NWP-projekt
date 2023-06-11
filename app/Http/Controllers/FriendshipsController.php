<?php

namespace App\Http\Controllers;

use App\Models\Auth;
use App\Models\Friendship;
use App\Models\User;
use App\Models\Notification;
use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        //Friendship::makeFriends($user, $friend);
        //$this->refreshFriends($userId);
        //$this->refreshUsersToAdd($userId);

        $friendRequest = new FriendRequest();
        $friendRequest->user_id = $user->id;
        $friendRequest->friend_id = $friend->id;
        $friendRequest->save();

        $notif = new Notification();
        $notif->content = $user->name . " has sent you a friend request.";
        $notif->type_id = $friendRequest->id;
        $notif->user_id = $user->id;
        $notif->friend_id = $friend->id;
        $notif->type = "friend_request";
        $notif->save();

        return view('addUsers', ['usersToAdd' => session(('usersToAdd'))]);
    }

    public function acceptFriend($userId, $friendId)
    {
        $user = User::find($userId);
        $friend = User::find($friendId);

        if (!$user || !$friend) {
            return response()->json(['message' => 'User or friend not found'], 404);
        }

        Friendship::makeFriends($user, $friend);
        $this->refreshFriends($userId);
        $this->refreshUsersToAdd($userId);

        $friendRequest = FriendRequest::where('friend_id', $userId)->where('user_id', $friendId);
        $friendRequest->delete();

        return view('friends-list', ['friends' => session('friends')]);
    }

    public function rejectFriend($userId, $friendId)
    {
        $friendRequest = FriendRequest::where('friend_id', $userId)->where('user_id', $friendId);
        $friendRequest->delete();   

        return view('friends-list', ['friends' => session('friends')]);
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
    public function saveFriendsJSON($userId){
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
        $friendsListJSON = array();
        foreach($friends as $key => $val)
        {
            $friendsListJSON[$key]['id'] = $val['id'];
            $friendsListJSON[$key]['name'] = $val['name'];
            $friendsListJSON[$key]['type'] = 'friend';            
        }
        $filename = 'users.json';
        return Storage::disk('local')->put($filename, json_encode($friendsListJSON)); 
    }

    public function searchFriends(Request $request)
    {
        $query = $request->input('query');
        $results = User::where('name', 'like', "%$query%")->get();
        
        return view('addUsers', compact('results'));
    }
}