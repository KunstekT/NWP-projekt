                                                  

<br>
<div class="card">     
    @foreach ($userCommentPairs as $pair)
    <div class="card" data-comment-id="{{ $pair['comment']->id }}" id="comment-{{ $pair['comment']->id }}">
        <p><strong>{{ $pair['user']->name }}:</strong> @php echo $pair['comment']->content @endphp
        @if($pair['user']->id == Auth::id())
            <button class="btn btn-danger deleteCommentButton float-end" data-post-id="{{$postId}}" data-comment-id="{{$pair['comment']->id}}">DELETE</button>
        @endif
        </p>
        {{ formatTimeAgo($pair['comment']->created_at) }}
    </div>
    @endforeach
</div>
<form method="POST" action="{{ route('postComment', ['postId' => $postId]) }}">
    @csrf
    <div class="form-group">
        <textarea class="mention form-control" name="content" rows="3" placeholder="Write your comment"></textarea>
    </div>
    <button type="submit" class="postMention btn btn-primary">Post Comment</button>
</form>

<script>
    
const deleteCommentButtons = document.querySelectorAll('.deleteCommentButton');

deleteCommentButtons.forEach(button => {
    button.addEventListener('click', (event) => {
        const postId = event.target.getAttribute('data-post-id');
        const commentId = event.target.getAttribute('data-comment-id');

        const url = `/posts/${postId}/showComments/${commentId}/delete`;
        axios.delete(url)
                .then(response => {
                    // Comment successfully deleted
                    console.log(response.data);        
                    
                    const commentElement = document.getElementById(`comment-${commentId}`);
                    if (commentElement) {
                        commentElement.remove();
                    }
                })
                .catch(error => {
                    // Error occurred while deleting the comment
                    console.error(error);
                });
    });
});

</script>