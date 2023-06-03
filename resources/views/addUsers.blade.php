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
                    <li class="list-group-item">{{ $userToAdd->name }}
                    <a class="btn btn-primary" href="{{ route('addFriend', ['userId' => Auth::id(), 'friendId' =>$userToAdd->id ]) }}" >Add</a>
                    </li>
                @empty
                    <li>Everyone is your friend! What a popular person you are!</li>
                @endforelse
            </div>


        </div>
    </div>
</div>

@endsection