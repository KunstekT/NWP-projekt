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
                            <div class="container">
                                    @if ($user->profile_image)
                                        <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt="Profile Image">
                                    @else
                                        <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/default.png') }}" alt="Default Image">
                                    @endif                                 
                                    
                                    <a href="{{ route('chatWith', ['receiverId' => $user->id]) }}" style="text-decoration:none;"><strong style="font-size:22px">{{ $user->name }} </strong></a>

                          
                            </div>                     
                        @endif
                    @endif
                @endforeach

                <!-- <div class="container">
                    <a href="{{ route('findFriends', ['userId' => Auth::id()]) }}" class="btn btn-primary stretched-link">Find friends!</a>
                </div> -->

            </div>
        </div>
        <div class="col-md-8">
            <div class="container overflow-auto end" style="height:500px">
                @if (isset($data))

                    <div class="container" id="dataContainer">
                        <h2>Chat with <a href="" style="text-decoration:none;">{{$receiver->name }}</a>:</h2>
                        <ul class="list-group" id="chatDisplay">
                        @foreach ($data as $item)
                            @if ($item->sender_id == Auth::id())
                            <div class="row">
                                <!-- {{  $sender->name }} said:  -->                                
                                <div class="card col-md-11 text-bg-info"style="min-height:50px">
                                    {{ $item->message }}
                                </div>
                                <div class="container col-md-1">
                                    @if ($user->profile_image)
                                        <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/' . $sender->profile_image) }}" alt="Profile Image">
                                    @else
                                        <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/default.png') }}" alt="Default Image">
                                    @endif    
                                </div>
                            </div>
                            @endif
                            @if ($item->receiver_id == Auth::id())
                            <div class="row">
                                <div class="container col-md-1">
                                @if ($user->profile_image)
                                    <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/' . $receiver->profile_image) }}" alt="Profile Image">
                                @else
                                    <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/default.png') }}" alt="Default Image">
                                @endif   
                                <!-- {{  $receiver->name }}  said:  -->
                                </div>
                                <div class="card col-md-11 text-bg-white" style="min-height:50px">
                                    {{ $item->message }}
                                </div>
                            </div>
                            @endif
                            <br>
                        @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div id="dataContainer"></div>

            @if(isset($receiverId))
                @if($receiverId >= 0)
                <!-- <form method="POST" action="{{ route('send.message',  ['receiverId' => $receiverId]) }}">
                    @csrf
                    <input type="hidden" name="receiverId" value="{{$receiverId}}">
                    <input type="text"class="form-control" name="message" id="message-input" placeholder="Enter your message">
                    <button id="sendButton" type="submit" class="btn btn-primary">Send</button>
                </form> -->

                <input type="hidden" name="receiverId" value="{{$receiverId}}">
                <input type="text"class="form-control" name="message" id="message-input" placeholder="Enter your message">
                <button id="sendButton" type="submit" class="btn btn-primary">Send</button>

                @endif
            @endif

        </div>
    </div>  
</div>

<script>
sendButton.addEventListener('click', function() {
    // Get the message content from the text input field
    const message = document.getElementById('message-input').value;
    const receiver_id = <?php echo $receiverId; ?>;
    
    axios.post('/send', {
        message: message,
        receiver_id: receiver_id
    })
    .then(function(response) {

        var updatedMessagesList = response.data.messageListHtml;

        var chatDisplay = document.getElementById('chatDisplay');

        chatDisplay.innerHTML = '';
        chatDisplay.innerHTML = updatedMessagesList;

        document.getElementById('message-input').value = '';
    })
    .catch(function(error) {
        console.error(error);
    });
});

</script>
@endsection

