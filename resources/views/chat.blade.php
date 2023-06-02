@extends('layouts.app')

@section('content')
    <h1>Chat</h1>
    <h2>List of Users:</h2>

    <ul>
        @foreach ($users as $user)
            @if ($user->id !== Auth::id())
                <li>User ID: {{ $user->id }}</li>
                <li>Name: {{ $user->name }}</li>
                <li>Email: {{ $user->email }}</li>

            <form method="POST" action="{{ route('getChatWithAUser', ['sender_id' => Auth::id(), 'receiver_id' => $user->id]) }}">
                @csrf
                <button type="submit">Chat!</button>
            </form>
                
                <br>
            @endif
        @endforeach
    </ul>
    <ul>
        @if (isset($data))

            <div id="dataContainer">
                <h2>Chat:</h2>
                <ul>
                    @foreach ($data as $item)
                        @if ($item->sender_id == Auth::id())
                            <li>{{  $username }} said: {{ $item->message }}</li>
                        @endif
                        @if ($item->receiver_id == Auth::id())
                            <li>{{  $receiverUsername }}  said: {{ $item->message }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
    </ul>
    <div id="dataContainer"></div>

    @if(isset($_senderId) and isset($_receiverId))
    <form method="POST" action="{{ route('send.message',  ['senderId' => $_senderId, 'receiverId' => $_receiverId]) }}">
        @csrf
        <input type="hidden" name="sender_id" value="{{$_senderId}}"> 
        <input type="hidden" name="receiver_id" value="{{$_receiverId}}"> <!-- Replace with actual receiver ID -->
        <input type="text" name="message" placeholder="Enter your message">
        <button type="submit">Send</button>
    </form>
    @endif


    <!-- <div id="messages">
        @foreach($messages as $message)
            <p>{{ $message->message }}</p>
        @endforeach
    </div> -->


    <!-- <form method="post" action="/chat/send">
        @csrf

        <input type="text" name="message" placeholder="Type your message...">
        <button type="submit">Send -->
    </form>  
@endsection

