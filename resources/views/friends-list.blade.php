@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="container">
                <h1>Friends List</h1>
            </div>


            @forelse($friends as $friend)
                <div class="list-group">
                    <li class="list-group-item">{{ $friend->name }}
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