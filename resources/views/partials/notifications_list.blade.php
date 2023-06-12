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
            @php
                $exists = App\Models\Post::where('id', $notification->type_id)->exists();
            @endphp
            @if ($notification->type == "post" && $exists)                                                
                <a class="dropdown-item" href="{{ route('post', ['post' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
                <p class="right-align">{{ formatTimeAgo($notification->created_at) }}</p>
            @elseif ($notification->type == "post" && !$exists)    
                <a class="dropdown-item" href="{{ route('posts', ['posts' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
                <p class="right-align">{{ formatTimeAgo($notification->created_at) }}</p>
            @elseif ($notification->type == "comment" && $exists)
                <a class="dropdown-item" href="{{ route('post', ['post' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
                <p class="right-align">{{ formatTimeAgo($notification->created_at) }}</p>
            @elseif ($notification->type == "comment" && !$exists)    
                <a class="dropdown-item" href="{{ route('posts', ['posts' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
                <p class="right-align">{{ formatTimeAgo($notification->created_at) }}</p>
            @elseif ($notification->type == "likes" && $exists)
                <a class="dropdown-item" href="{{ route('post', ['post' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
                <p class="right-align">{{ formatTimeAgo($notification->created_at) }}</p>
                @elseif ($notification->type == "likes" && !$exists)    
                <a class="dropdown-item" href="{{ route('posts', ['posts' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
                <p class="right-align">{{ formatTimeAgo($notification->created_at) }}</p>
            @elseif ($notification->type == "friend_request")
                <a class="dropdown-item " href="{{ route('friends', ['userId' => Auth::id()]) }}">@php echo $notification->content @endphp</a>
                <p class="right-align">{{ formatTimeAgo($notification->created_at) }}</p>
            @endif
        @endif
        @empty
            <p>No notifications!</p>
    @endforelse
@endif