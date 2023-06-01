<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>ASDASDASD</title>
    <style>
        body {
            padding: 0;
            margin: 0;
            background: #f2f6e9;
        }

        .navbar {
            background: #6ab446;
        }

        .nav-link {
            color: #fff;
            cursor: pointer;
        }

        .nav-link {
            margin-right: 1em !important;
        }

        .nav-link:hover {
            color: #000;
        }

        .navbar-collapse {
            justify-content: flex-end;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
</head>
<body>
<div>
    <nav class="navbar navbar-expand-md">
        <button class="navbar-toggler navbar-dark" type="button" data-toggle="collapse" data-target="#main-navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main-navigation">
            <ul class="navbar-nav">
                @php($user = Auth::user())
                @if($user != null)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('menus') }}">{{ __('messages.menus') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('posts') }}">{{ __('messages.posts') }}</a>
                    </li>
                    @if($user->role != null && $user->role->id == 1)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('administrations') }}">{{ __('messages.administration') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('roles') }}">{{ __('messages.roles') }}</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('messages.logout') }}</a>
                    </li>
                    <form class="form-inline my-2 my-lg-0" id="change-language-form"
                          action="{{ route('changeLanguage') }}" method="POST">
                        {{ csrf_field() }}
                        <select onchange="this.form.submit()" name="language" id="language">
                            <option @if(App::isLocale('hr')) selected @endif value="hr">HR</option>
                            <option @if(App::isLocale('en')) selected @endif value="en">EN</option>
                        </select>
                    </form>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('register') }}">Register</a>
                    </li>
                @endif
            </ul>
            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                  style="display: none;">{{ csrf_field() }}</form>
        </div>
    </nav>
</div>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <!-- Links -->
  <ul class="navbar-nav">
    <!-- Dropdown -->
    @foreach ($menus as $menu)
        <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
            {{$menu->name}}
        </a>
        <div class="dropdown-menu">
            @foreach ($menu->posts as $menuPost)
                <a class="dropdown-item" href="{{ url('posts/details/'.$menuPost->id) }}">{{$menuPost->pivot->name}}</a>
            @endforeach
        </div>
        </li>
    @endforeach
  </ul>
</nav>
@yield('content')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"
        integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s"
        crossorigin="anonymous"></script>
</body>
</html>
