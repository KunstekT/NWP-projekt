@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1>Posts</h1>
            <p>What's on your mind?</p>
            <form method="POST" action="{{ route('post.create') }}">
                @csrf
                <textarea class="mention form-control" name="content"></textarea>
                <br>
                <div class="row justify-content-center">
                    <button class="postMention btn btn-primary" type="submit">Post</button>
                </div>
            </form>
            <br>
            @include('partials.posts_list')
        </div>
    </div>
</div>

@endsection