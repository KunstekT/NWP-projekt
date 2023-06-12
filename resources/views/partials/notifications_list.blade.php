@php

    use App\Models\Notification;

    function checkMentionCount($friendId){
        $mentionCount = Notification::where('friend_id', $friendId)->count();
        return $mentionCount;
    }
@endphp

@if(checkMentionCount(Auth::user()->id)==0) 
    <p>No notifications!</p>
@else
    @forelse($notifications as $notification)
        @if($notification->friend_id == Auth::user()->id)
            @if ($notification->type == "post")
                <a class="dropdown-item" href="{{ route('post', ['post' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
            @elseif ($notification->type == "comment")
                <a class="dropdown-item" href="{{ route('post', ['post' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
            @elseif ($notification->type == "likes")
                <a class="dropdown-item" href="{{ route('post', ['post' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
            @elseif ($notification->type == "friend_request")
                <a class="dropdown-item " href="{{ route('friends', ['userId' => Auth::id()]) }}">@php echo $notification->content @endphp</a>
            @endif
        @endif
        @empty
            <p>No notifications!</p>
    @endforelse
@endif