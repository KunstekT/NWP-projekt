@php
    use App\Models\Notification;
    $notifications = Notification::orderByDesc('created_at')->get();

    function checkMentionCount($friendId){
        $mentionCount = Notification::where('friend_id', $friendId)->count();
        return $mentionCount;
    }
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
</head>
<body>
    <div id="app">
    <!-- @auth
        Logged in as: {{ Auth::user()->name }} 
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @endauth -->

        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <div class="card" style="background-color: #3B5998;">
                <a class="navbar-brand" href="{{ route('home') }}" style="text-align:center;color:white;font-weight:bold" >mF</a>
            </div>
            <ul class="navbar-nav">
                <!-- Add more menu items as needed -->
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ Str::startsWith(request()->path(), 'posts') ? 'active' : '' }}" href="{{ route('posts') }}">Posts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{  Str::startsWith(request()->path(), 'profile') ? 'active' : '' }}" href="{{ route('profile', ['userId' => Auth::id()]) }}">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{  Str::startsWith(request()->path(), 'chat') ? 'active' : '' }}" href="{{ route('chat') }}">Chat</a>               
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{  (Str::startsWith(request()->path(), 'friends')||Str::startsWith(request()->path(), 'findFriends') ) ? 'active' : '' }}" href="{{ route('friends', ['userId' => Auth::id()]) }}">Friends</a>
                    </li>
                @endauth
            </ul>
        </div>
            <div class="container">
                <!-- <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a> -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="dropdown nav-item">
                                <button class="dropdown-toggle notif-button
                                " data-bs-toggle="dropdown">
                                    <img class="nav-icon" src="{{ asset('storage/profile_images/notification_icon.png') }}" alt="Notifications"></img>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if(checkMentionCount(Auth::user()->id)==0) 
                                        <p>No notifications!</p>
                                    @else
                                        @forelse($notifications as $notification)
                                            @if($notification->friend_id == Auth::user()->id)
                                                @if ($notification->type == "post")
                                                    <a class="dropdown-item" href="{{ route('posts', ['postId' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
                                                @elseif ($notification->type == "comment")
                                                    <a class="dropdown-item" href="{{ route('showComments', ['postId' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
                                                @elseif ($notification->type == "likes")
                                                    <a class="dropdown-item" href="{{ route('posts', ['postId' => $notification->type_id]) }}">@php echo $notification->content @endphp</a>
                                                @elseif ($notification->type == "friend_request")
                                                    <a class="dropdown-item " href="{{ route('friends', ['userId' => Auth::id()]) }}">@php echo $notification->content @endphp</a>
                                                @endif
                                            @endif
                                            @empty
                                                <p>No notifications!</p>
                                        @endforelse
                                    @endif
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
            @yield('script')
        </main>
    </div>
    @section('script')
    <script>
        function scrollToPost(postId) {
            const postElement = document.getElementById('postId');;
            postElement.scrollIntoView({ behavior: 'smooth' });
        }
    </script>
    @endsection('script')
</body>
</html>
