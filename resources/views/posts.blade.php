
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
                <textarea class="mention form-control" name="content"></textarea>
                <br>
                <div class="row justify-content-center">
                    <button class="postMention btn btn-primary" type="submit">Post</button>
                </div>
            </form>
            <br>
            @include('partials.posts_list', ['showUserOnly' => false, 'routeName' => 'posts'])
        </div>
    </div>
</div>

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

@endsection