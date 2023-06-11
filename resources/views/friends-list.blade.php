@extends('layouts.app')

@php
    use App\Models\Notification;
    use App\Models\FriendRequest;
    $friendRequests = FriendRequest::where('friend_id', Auth::user()->id)->orderByDesc('created_at')->get();
    $notifications = Notification::orderByDesc('created_at')->get();

    function checkFriendRequestCount($friendId){
        $mentionCount = Notification::where('friend_id', $friendId)->where('type', 'friend_request')->count();
        return $mentionCount;
    }
@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div>
                <h1>Friend requests</h1>
                <div class="list-group">                    
                        @if(checkFriendRequestCount(Auth::user()->id)==0) 
                            <p>No friend requests.</p>
                        @else
                            <li class="list-group-item">
                                @forelse($friendRequests as $friendRequest)
                                    @forelse($notifications as $notification)                                    
                                        @if($notification->friend_id == Auth::user()->id)
                                            @if ($notification->type == "friend_request")                                        
                                                @if($notification->type_id == $friendRequest->id)
                                                    <a class="dropdown-item" href="{{ route('posts', ['postId' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
                                                    <a class="btn btn-primary" href="{{ route('acceptFriend', ['userId' => Auth::id(), 'friendId' =>$notification->user_id ]) }}" title="">Accept</a>
                                                    <a class="btn btn-danger" href="" title="">Reject</a>
                                                @endif                                        
                                            @endif
                                        @endif    
                                        @empty
                                            <p>No friend requests.</p>
                                    @endforelse
                                @empty 
                                    <p>No friend requests.</p>  
                                @endforelse
                            </li>
                        @endif                    
                </div>
            </div>
            <div class="container">
                <h1>Friends List</h1>
            </div>


            @forelse($friends as $friend)
                <div class="list-group">
                    <li class="list-group-item">
                        
                    @if ($friend->profile_image)
                        <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/' . $friend->profile_image) }}" alt="Profile Image">
                    @else
                        <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/default.png') }}" alt="Default Image">
                    @endif                                 
                        <a href="{{ route('profile', ['userId'=> $friend->id]) }}" style="text-decoration:none;"><strong style="font-size:22px">{{ $friend->name }}</strong></a>
                    
                        <a class="btn btn-danger" href="{{ route('removeFriend', ['userId' => Auth::id(), 'friendId' => $friend->id ]) }}" title="Remove {{$friend->name}} from your friends list">Unfriend</a>
                    </li>
                </div>

                @empty
                <li class="list-group-item">No friends found</li>
            @endforelse
            <br>        
            <div class="container">
                <a class="btn btn-primary" href="{{ route('findFriends', ['userId' => Auth::id()]) }}">Find new friends</a>
            </div>
        </div>
    </div>
</div>
@endsection