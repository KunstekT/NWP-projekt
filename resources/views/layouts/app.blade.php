<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My App</title>
</head>
<body>
    <header>
    @auth
        Logged in as: {{ Auth::user()->name }} 
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @endauth
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">MCP NAVBAR! (MCP = "Mini chat project", yes, rename me... im in 'app.blade.php')</a>
            <ul class="navbar-nav">
                <!-- Add more menu items as needed -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('chat') }}">Chat ...Add other links here when logged in like, friends list, chat (this button moves to chat) etc.</a>               
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('friends', ['userId' => Auth::id()]) }}">Friends</a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>
    
    <p>----------------------- This is the end of the header (what a fat header...) -----------------------</p>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <!-- Footer content here -->
    </footer>
</body>
</html>