@php
use App\Models\Post;
use App\Models\Notification;
use Illuminate\Database\Query\Builder;

    $posts = Post::orderByDesc('created_at')->get();

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

    function userIsMentioned($post_id, $friend_id){
        $foundPostId = false;
        if(Notification::where('type', 'post')->where('friend_id', $friend_id)->get() == null){
            return false;
        }
        $mentioned_posts_ids = Notification::where('type', 'post')->where('friend_id', $friend_id)->get();

        foreach ($mentioned_posts_ids as $mentioned_posts_id){
            if($post_id == $mentioned_posts_id->type_id){
                $foundPostId = true;
            }
        }
        return $foundPostId;
    }        
@endphp

<ul class="list-group">
@forelse($posts as $post)
@php
        $postOwner = \App\Models\User::find($post->user_id);
@endphp
    @isset($profileUser)        
        @if($postOwner->id != $profileUser->id && !userIsMentioned($post->id, $profileUser->id))
            @continue
        @endif
    @else
        @if(!(userIsAFriend($post->user_id) || ($post->user_id == Auth::id())))
            @continue
        @endif
    @endisset


    <div class="card">
        <div class="row">
            <div class="col-md-6 mx-auto">   
            @if ($postOwner->profile_image)
                <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/' . $postOwner->profile_image) }}" alt="Profile Image">
            @else
                <img id="profileImage" class="img-thumbnail mx-auto" style="width:50px;height:50px" src="{{ asset('storage/profile_images/default.png') }}" alt="Default Image">
            @endif                                 
                <a href="{{ route('profile', ['userId'=> $post->user_id]) }}" style="text-decoration:none;"><strong style="font-size:22px">{{ $post->user->name }}</strong></a>
            </div>
            <div class="col-md-6 ms-auto">
            @if($post->user_id == Auth::user()->id)
                <form class="form-group float-end" action="{{ route('posts.delete', ['postId' => $post->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Post</button>
                </form>
                <a href="{{ route('posts.edit', ['postId' => $post->id]) }}" class="btn btn-primary float-end edit-button">Edit Post</a>                  
                <!-- <button class="btn btn-primary float-end edit-button">Edit Post</button>                   -->
            @endif
            </div>
        </div>
        
        <p data-post-id="<?= $post->id ?>"> @php echo $post->content @endphp</p>

        {{ formatTimeAgo($post->created_at) }}<br>
        <p>Likes: <span id="like-count-{{ $post->id }}">{{ $post->likes()->count() }}</span>
        <span> Comments: {{ $post->comments()->count() }}</span></p>   

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">                 
                    @if(Auth::user()->likedPosts->contains($post))
                        <button id="likeButton" class="btn btn-light btn-sm btn-block like-button" style="width:100%" data-post-id="{{ $post->id }}">Unlike</button>                        
                    @else
                        <button id="likeButton" class="btn btn-light btn-sm btn-block like-button" style="width:100%" data-post-id="{{ $post->id }}">Like</button>
                    @endif
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
                @include('partials.comments_list', ['postId' =>$post->id,'userCommentPairs' => $userCommentPairs])
            @endif
        @endisset
    </div>
    <br><br>
@empty
    <li>No posts. Post something!</li>
@endforelse
</ul>


<script>
    const likeButtons = document.querySelectorAll('.like-button');

    likeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const postId = button.dataset.postId;
            axios.post('/like', { postId: postId })
            .then(function(response) {
                console.log("like count: "+response.data.likeCount);
                console.log("liked: "+response.data.liked); 
                if (response.data.liked) {
                    button.textContent = 'Unlike';
                } else {
                    button.textContent = 'Like';
                }

                const likeCountElement = document.getElementById('like-count-' + postId);
                if (likeCountElement) {
                    likeCountElement.textContent = response.data.likeCount;
                }
            })
            .catch(error => {
                console.error(error);
            });
        });
    });
</script>