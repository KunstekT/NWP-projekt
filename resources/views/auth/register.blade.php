@extends('layouts.app')

@section('content')
        <h1>Register</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
            
            <div class="form-group">
                <a href="{{ route('login') }}">back to log in</a>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
@endsection