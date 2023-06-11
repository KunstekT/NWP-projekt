@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="container">
                <h1>Mini facebook</h1>
                <p>This is the place where you can make some friends!<br>
                You can also chat with them, or not if you feel like they don't deserve your attention!<br>
                You can also publish posts all your friends will see, unless you don't deserve their attention!</p>

                @guest
                    <div class="card">
                        <div class="card-header">
                            Log in or create a new account!
                        </div>
                        <div class ="card">
                        <a class="btn  btn-primary" href="{{ route('login') }}">Log in</a>
                    </div>
                    <div class="card">
                        <a class="btn  btn-primary"  href="{{ route('register') }}">Register</a>
                    </div>
                @endguest

                @auth
                <form class="form-group" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-primary" type="submit">Logout</button>
                </form>
                @endauth

        </div>


        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        </div>
    </div>
</div>

@endsection
