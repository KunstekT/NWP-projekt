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
                @if(session('friends') !== null)
                
                    @if(count(session('friends'))==0)
                    <div class="container">
                        <a href="{{ route('findFriends', ['userId' => Auth::id()]) }}" class="btn btn-primary stretched-link">Find friends!</a>
                    </div> 
                    @endif
                @endif

            </div>
        </div>
        <div class="col-md-8">
            <div class="container overflow-auto end" id="scrolldiv" style="height:500px">
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
                <input type="hidden" name="receiverId" value="{{$receiverId}}">
                <input type="text"class="form-control" name="message" id="message-input" placeholder="Enter your message">
                <button id="sendButton" type="submit" class="btn btn-primary">Send</button>

                @endif
            @endif

        </div>
    </div>  
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>

    // Initialize Pusher
    const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        encrypted: true
    });

    // Subscribe to the chat channel
    const channel = pusher.subscribe('chat');

    // Listen for the 'chat-message-sent' event
    channel.bind('message-created', function(data) {
        // Handle the received chat message
        console.log(data.message);
    });

   // Sending a message
function sendMessage(receiverId, message) {
    console.log(receiverId);
  axios.post('/send', {
    // senderId: {{Auth::id()}},
    receiver_id: receiverId,
    message: message
  })
  .then(response => {
    // Message sent successfully, update UI if needed

    console.log('Message sent:', response.data);
  })
  .catch(error => {
    // Handle error
    console.error('Error sending message:', error);
  });
}

// Receiving messages
function receiveMessages(receiverId) {
  axios.get('/receive', {
    params: {
      receiverId: receiverId,
    }
  })
  .then(response => {
    // Handle received messages

    const updatedMessagesList = response.data.messageListHtml;
    // console.log('Received messages:', updatedMessagesList);
    var chatDisplay = document.getElementById('chatDisplay');
    chatDisplay.innerHTML = '';
    chatDisplay.innerHTML = updatedMessagesList;

    var container = document.getElementById('scrolldiv');
    container.scrollTop = container.scrollHeight;
    })
  .catch(error => {
    // Handle error
    console.error('Error receiving messages:', error);
  });
}

  // Event handler for send button click
  document.getElementById('sendButton').addEventListener('click', function () {
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value;
    sendMessage({{$receiverId}}, message);
    messageInput.value = ''; // Clear the input field
  });

  // Call receiveMessages initially and then periodically
  receiveMessages({{$receiverId}});
  setInterval(receiveMessages, 1000, {{$receiverId}});

</script>
@endsection

