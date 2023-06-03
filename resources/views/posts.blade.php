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
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1>Posts</h1>
            <p>What's on your mind?</p>
            <form method="POST" action="{{ route('post.create') }}">
                @csrf
                <textarea class="form-control" name="content"></textarea>
                <br>
                <div class="row justify-content-center">
                    <button class="btn btn-primary" type="submit">Post</button>
                </div>
            </form>
            <br>
            <ul class="list-group">
                @forelse($posts as $post)
                    @if($post->user_id == Auth::id() || userIsAFriend($post->user_id))
                        <div class="card">
                                <strong>{{ $post->user->name }}</strong> 
                                <p> {{ $post->content }}</p>
                                {{ formatTimeAgo($post->created_at) }}
                            
                        </div>
                    @endif  
                @empty
                    <li>No posts</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>


@endsection