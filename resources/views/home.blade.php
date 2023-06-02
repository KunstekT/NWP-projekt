@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Welcome to the mini chat app and stuff. I need a rename!</h1>
        <p>This is the plase where you can make some friends!<br>
        You can also chat with them, or not if you feel like they don't deserve your attention!<br>
        You can also publish posts all your friends will see, unless you don't deserve their attention!</p>
        @guest
        <p>Log in or create a new account!</p>
            <div>
                <a href="{{ route('login') }}">Log in</a>
            </div>
            <div>
                <a href="{{ route('register') }}">Register</a>
            </div>
        @endguest
        @auth
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
            <!-- <div>
                <a href="{{ route('logout') }}">Log out</a>
            </div> -->
        @endauth

    </div>

    <script src="{{ asset('js/app.js') }}"></script>
@endsection