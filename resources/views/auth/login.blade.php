@extends('layouts.app')

@section('content')
        <h1>Log In</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember"> Remember Me
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Log In</button>
            </div>

            <div class="form-group">
                <a href="{{ route('register') }}">Need an account?</a>
            </div>
            <div class="form-group">
                <a href="{{ route('chat') }}">??? Chat</a>
            </div>

        </form>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
@endsection