@extends('layouts.app')

@section('content')
    <div>
        <h1>Add to friends</h1>

        <ul>
            @forelse($usersToAdd as $userToAdd)
                <li>{{ $userToAdd->name }}</li>
                <a href="{{ route('addFriend', ['userId' => Auth::id(), 'friendId' =>$userToAdd->id ]) }}" >Add to friends list</a>
            @empty
                <li>Everyone is your friend! What a popular person you are!</li>
            @endforelse
        </ul>

    </div>

@endsection