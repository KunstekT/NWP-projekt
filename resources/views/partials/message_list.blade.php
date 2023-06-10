@foreach ($messages as $item)
    @if ($item->sender_id == Auth::id())
    <div class="row">
        <!-- {{  $sender->name }} said:  -->                                
        <div class="card col-md-11 text-bg-info"style="min-height:50px">
            {{ $item->message }}
        </div>
        <div class="container col-md-1">
            @if ($sender->profile_image)
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
        @if ($receiver->profile_image)
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