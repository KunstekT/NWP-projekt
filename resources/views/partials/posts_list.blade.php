
@php
use App\Models\Post;
    $posts = Post::all();

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

    function hasLikedThePost($post){
        
        return false;
    }

@endphp

<ul class="list-group">
@forelse($posts as $post)
    @if(userIsAFriend($post->user_id) == true && $showUserOnly == true) 
        @continue
    @endif
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
            <p>Likes: <span id="like-count-{{ $post->id }}">{{ $post->likes()->count() }}</span>
            <span> Comments: {{ $post->comments()->count() }}</span></p>   

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        
                            <div id="likeButtonPlace">
                                @csrf
                                <button id="likeButton" class="btn btn-light btn-sm btn-block like-button" type="submit" style="width:100%" data-post-id="{{ $post->id }}">Like</button>
                                

                                    <!-- <button id="likeButton" class="btn btn-light btn-sm btn-block" type="submit" style="width:100%" >Unlike</button> -->

                            </div>
                        
                    </div>
                </div>
                <div class="col-md-6">
                    @if(Str::startsWith(request()->path(), 'posts'))
                    <div class="card">
                        <a class="btn btn-light btn-sm btn-block" href="{{ route('showComments', ['postId' => $post->id]) }}" style="width:100%">Comment</a>
                    </div>   
                    @else
                    <div class="card">
                        <a class="btn btn-light btn-sm btn-block" href="{{ route('showCommentsInProfilePage', ['postId' => $post->id]) }}" style="width:100%">Comment</a>
                    </div>  
                    @endif
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


<script>
    // Get all like buttons
    const likeButtons = document.querySelectorAll('.like-button');

    // Attach click event listener to each like button
    likeButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Get the post ID from the data attribute
            const postId = button.dataset.postId;

            // Send AJAX request to toggle the like status
            axios.post('/like', { postId: postId })
            .then(function(response) {
                if (response.data.liked) {
                    button.textContent = 'Unlike';
                } else {
                    button.textContent = 'Like';
                }

                const likeCountElement = document.getElementById('like-count-' + postId);
                if (likeCountElement) {
                    likeCountElement.textContent = response.data.likeCount;
                }

                // window.location.reload();
            })
            .catch(error => {
                // Handle any errors
                console.error(error);
            });
        });
    });
</script>