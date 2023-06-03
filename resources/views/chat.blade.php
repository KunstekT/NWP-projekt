@php

function userIsAFriend($userId) {
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
        <div class="col-md-4">

            <h2>Chat with a friend!</h2>

            <div class="container">        
                @foreach ($users as $user)
                    @if ($user->id !== Auth::id())
                        @if(userIsAFriend($user->id))
                            <div class="card" style="width: 36rem;">
                            <div class="card-body">
                                <p class="card-text">{{ $user->name }} ({{ $user->email }}) 
                                <a href="{{ route('getChatWithAUser', ['sender_id' => Auth::id(), 'receiver_id' => $user->id]) }}" class="btn btn-primary stretched-link">Chat</a>
                                </p>
                            </div>
                            </div>                     
                        @endif
                    @endif
                @endforeach

                <!-- <div class="container">
                    <a href="{{ route('findFriends', ['userId' => Auth::id()]) }}" class="btn btn-primary stretched-link">Find friends!</a>
                </div> -->

            </div>
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6">
            <div class="container">
                @if (isset($data))

                    <div class="container" id="dataContainer">
                        <h2>Chat with {{$receiverUsername }}:</h2>
                        <ul class="list-group">
                            @foreach ($data as $item)
                                @if ($item->sender_id == Auth::id())
                                    <li class="list-group-item">{{  $username }} said: {{ $item->message }}</li>
                                @endif
                                @if ($item->receiver_id == Auth::id())
                                    <li class="list-group-item">{{  $receiverUsername }}  said: {{ $item->message }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div id="dataContainer"></div>

            @if(isset($_senderId) and isset($_receiverId))
            <form method="POST" action="{{ route('send.message',  ['senderId' => $_senderId, 'receiverId' => $_receiverId]) }}">
                @csrf
                <input type="hidden" name="sender_id" value="{{$_senderId}}"> 
                <input type="hidden" name="receiver_id" value="{{$_receiverId}}">
                <input type="text"class="form-control" name="message" placeholder="Enter your message">
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
            @endif

        </div>
    </div>  
</div>
@endsection

