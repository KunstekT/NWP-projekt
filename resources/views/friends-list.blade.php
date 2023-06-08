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