@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="container">
                <h1>Add to friends</h1>
            </div>
            <div class="list-group">
                @forelse($usersToAdd as $userToAdd)
                    <li class="list-group-item">
                    @if ($userToAdd->profile_image)
                        <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/' . $userToAdd->profile_image) }}" alt="Profile Image">
                    @else
                        <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/default.png') }}" alt="Default Image">
                    @endif                                 
                        <a href="{{ route('profile', ['userId'=> $userToAdd->id]) }}" style="text-decoration:none;"><strong style="font-size:22px">{{ $userToAdd->name }}</strong></a>
                        @php
                            $countreq1 = App\Models\FriendRequest::where('user_id', Auth::user()->id)->where('friend_id', $userToAdd->id)->orderByDesc('created_at')->count(); 
                            $countreq2 = App\Models\FriendRequest::where('user_id', Auth::user()->id)->where('friend_id', $userToAdd->id)->orderByDesc('created_at')->count(); 
                        @endphp
                        @if(!$countreq1!= 0 || !$countreq2!= 0)
                            <a class="btn btn-primary" href="{{ route('addFriend', ['userId' => Auth::id(), 'friendId' =>$userToAdd->id ]) }}" >Add</a>
                        @else
                            <a class="btn btn-secondary" disabled>Sent</a>
                        @endif
                    </li>
                @empty
                    <li class="list-group-item">Everyone is your friend! What a popular person you are!</li>
                @endforelse
            </div>


        </div>
    </div>
</div>

@endsection