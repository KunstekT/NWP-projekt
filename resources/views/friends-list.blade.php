@extends('layouts.app')

@section('content')
    <h1>Friends List</h1>

    <ul>
        @forelse($friends as $friend)
            <li>{{ $friend->name }}</li>
            <a href="{{ route('removeFriend', ['userId' => Auth::id(), 'friendId' => $friend->id ]) }}">Remove friend</a>
        @empty
            <li>No friends found</li>
        @endforelse
    </ul>

    <a href="{{ route('findFriends', ['userId' => Auth::id()]) }}">Find new friends</a>
@endsection