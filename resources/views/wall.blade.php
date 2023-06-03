@php
    function formatTimeAgo($dateTime)
    {
        $now = now();
        $diff = $dateTime->diffInSeconds($now);

        if ($diff < 60) {
            return $diff . ' seconds ago';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' minutes ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hours ago';
        } else {
            $days = floor($diff / 86400);
            return $days . ' days ago';
        }
    }

    function userIsAFriend($userId) {
        // return in_array($userId, $friends);
        $friends = session('friends', []);
        foreach (session('friends', []) as $friend) {
            if ($friend->id === $userId) {
                return true;
            }
        }
        return false;
    }

@endphp

@extends('layouts.app')

@section('content')
<h1>Wall</h1>
<p>See what is on people's minds</p>

<form method="POST" action="{{ route('post.create') }}">
    @csrf
    <textarea name="content"></textarea>
    <button type="submit">Post</button>
</form>

<ul>
    @forelse($posts as $post)
        @if($post->user_id == Auth::id() || userIsAFriend($post->user_id))
            <div><br>
                    <strong>Posted by:</strong> {{ $post->user->name }}<br>
                    <p> {{ $post->content }}</p><br>
                    <strong>Created:</strong> {{ formatTimeAgo($post->created_at) }}
                <br>---------------------------------------------
            </div>
        @endif  
    @empty
        <li>No posts</li>
    @endforelse
</ul>


@endsection