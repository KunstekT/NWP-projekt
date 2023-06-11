@extends('layouts.app')

@php
use App\Models\User;
function formatTimeAgo($dateTime)
{
    $now = now();
    $diff = $dateTime->diffInSeconds($now);

    if ($diff < 60) {
        return $diff . ' seconds ago';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minutes ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hours ago';
    } else {
        $days = floor($diff / 86400);
        return $days . ' days ago';
    }
}

$singlePost = $post;
$singlePostOwner = User::where('id', $post->user_id)->first();
@endphp
@section('content')
<div class="container">
<div id="floatingDiv" class="floating-div">
    <div id="floatingContentDiv" class="card floating-content-div">
        
        <br>
        <div class="row">
            <div class="col-md-6 mx-auto">   
            @if ($singlePostOwner->profile_image)
                <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/' . $singlePostOwner->profile_image) }}" alt="Profile Image">
            @else
                <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/default.png') }}" alt="Default Image">
            @endif                                 
            <a href="{{ route('profile', ['userId'=> $singlePost->user_id]) }}" style="text-decoration:none;"><strong style="font-size:22px">{{ $singlePost->user->name }}</strong></a>
            </div>
            <div class="col-md-6 ms-auto">
            <a class="btn btn-danger mx-auto float-end" href="{{ route('posts') }}">Close</a> <!--"closeFloatingDiv()"!-->
            @if($singlePost->user_id == Auth::user()->id)
                <form class="form-group float-end" action="{{ route('posts.delete', ['postId' => $singlePost->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Post</button>
                </form>

                <button id="editButton" class="btn btn-primary float-end edit-button" data-post-id="{{ $singlePost->id }}">Edit</button>
            @endif
            </div>
        </div>
        
        <div id="contentSection" style="display: all;">
            
            <p id="postContent" data-post-id="{{ $singlePost->id }}"> @php echo $singlePost->content @endphp</p>
            
        </div>

        <div id="editSection" style="display: none;">
            <textarea class="form-control" name="content"id="editTextField">@php echo $singlePost->content @endphp</textarea>
            <button action="PATCH" id="updateButton" class="btn btn-primary" data-post-id="{{ $singlePost->id }}">Update</button>
        </div>

        {{ formatTimeAgo($singlePost->created_at) }}<br>
        <p>Likes: <span id="like-count-{{ $singlePost->id }}">{{ $singlePost->likes()->count() }}</span>
        <span> Comments: {{ $singlePost->comments()->count() }}</span></p>   

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">                 
                    @if(Auth::user()->likedPosts->contains($singlePost))
                        <button id="likeButton" class="btn btn-light btn-sm btn-block like-button" style="width:100%" data-post-id="{{ $singlePost->id }}">Unlike</button>                        
                    @else
                        <button id="likeButton" class="btn btn-light btn-sm btn-block like-button" style="width:100%" data-post-id="{{ $singlePost->id }}">Like</button>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <a class="btn btn-light btn-sm btn-block" href="{{ route('showCommentsInSinglePost', ['postId' => $singlePost->id]) }}" style="width:100%">Comment</a>
                </div>  
            </div>
        </div>  
        @php
            $controller = app('App\Http\Controllers\PostController');
            $userCommentPairs = $controller->getUserCommentPairs($singlePost->id);
        @endphp
        <div class="card">                               
            @include('partials.comments_list', ['postId' => $singlePost->id,'userCommentPairs' => $userCommentPairs])


        </div>  
    </div>
    </div>

@endsection
