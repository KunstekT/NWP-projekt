@php
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

    function userIsAFriend($userId) {
        // return in_array($userId, $friends);
        $friends = session('friends', []);
        foreach (session('friends', []) as $friend) {
            if ($friend->id === $userId) {
                return true;
            }
        }
        return false;
    }

@endphp
<!-- <script>
    function toggleLike(button) {
        var postId = button.dataset.postId;

        axios.post(`/posts/${postId}/like`)
            .then(function (response) {
                // Update the like count
                var likeCount = button.querySelector('.like-count');
                likeCount.textContent = response.data.liked ? parseInt(likeCount.textContent) + 1 : parseInt(likeCount.textContent) - 1;
            })
            .catch(function (error) {
                console.log(error);
            });
    }
</script> -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1>Posts</h1>
            <p>What's on your mind?</p>
            <form method="POST" action="{{ route('post.create') }}">
                @csrf
                <textarea class="form-control" name="content"></textarea>
                <br>
                <div class="row justify-content-center">
                    <button class="btn btn-primary" type="submit">Post</button>
                </div>
            </form>
            <br>
            <ul class="list-group">
                @forelse($posts as $post)
                    @if($post->user_id == Auth::id() || userIsAFriend($post->user_id))
                        <div class="card">
                            <div class="row">
                            @php
                                $user = \App\Models\User::find($post->user_id);
                            @endphp
                                <div class="col-md-6 mx-auto">   
                                @if ($user->profile_image)
                                    <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt="Profile Image">
                                @else
                                    <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/default.png') }}" alt="Default Image">
                                @endif                                 
                                    <a href="{{ route('profile', ['userId'=> $post->user_id]) }}" style="text-decoration:none;"><strong style="font-size:22px">{{ $post->user->name }}</strong></a>
                                </div>
                                <div class="col-md-6 ms-auto">
                                @if($post->user_id == Auth::id())
                                    <form class="form-group float-end" action="{{ route('posts.delete', ['postId' => $post->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete Post</button>
                                    </form>
                                    <a href="{{ route('posts.edit', ['postId' => $post->id]) }}" class="btn btn-primary float-end">Edit Post</a>
                                    
                                @endif
                                </div>
                            </div>
                            
                            <p> {{ $post->content }}</p>


                            {{ formatTimeAgo($post->created_at) }}<br>
                            Likes: {{ $post->likes()->count() }}, Comments: {{ $post->comments()->count() }}

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="card">
                                        <form method="POST" action="{{ route('like', ['postId' => $post->id]) }}">
                                            @csrf                                          
                                            <button class="btn btn-light btn-sm btn-block" type="submit" style="width:100%" >Like</button>
                                        </form>  
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <a class="btn btn-light btn-sm btn-block" href="{{ route('showComments', ['postId' => $post->id]) }}" style="width:100%">Comment</a>
                                    </div>   
                                </div>
                            </div>  

                                @isset($postWithOpenedCommentsId)                                                       
                                    @if($postWithOpenedCommentsId == $post->id)
                                    <br>
                                    <div class="card">     
                                        @foreach ($userCommentPairs as $pair)
                                        <div class="card">
                                            <p><strong>{{ $pair['user']->name }}:</strong> {{ $pair['comment']->content }}</p>
                                            {{ formatTimeAgo($pair['comment']->created_at) }}
                                        </div>
                                        @endforeach
                                    </div>
                                    <form method="POST" action="{{ route('postComment', ['postId' => $post->id]) }}">
                                        @csrf

                                        <div class="form-group">
                                            <textarea class="form-control" name="content" rows="3" placeholder="Write your comment"></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Post Comment</button>
                                    </form>
                                    @endif
                                @endisset
                        </div>
                    @endif  
                    <br><br>
                @empty
                    <li>No posts</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>


@endsection