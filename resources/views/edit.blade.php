

@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
    <form method="POST" action="{{ route('updatePost', ['postId' => $post->id]) }}">
        @csrf

        <div class="form-group">
            <label for="content">Edit post</label>
            <textarea class="form-control" name="content" id="content" rows="3" required>{{ $post->content }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
    </div>
    <div class="col-md-2"></div>
</div>
@endsection