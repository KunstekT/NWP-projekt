<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
@include('partials._navbar')
    <div class="container">
        <h1>Welcome to the mini chat app and stuff. I need a rename!</h1>

            <div>
                <a href="{{ route('login') }}">Log in</a>
            </div>
            <div>
                <a href="{{ route('register') }}">Register</a>
            </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>