<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\Post;
use App\Models\Like;
use App\Models\User;
use App\Models\Friendship;
use App\Models\Comment;
use App\Models\Notification;

class PostController extends Controller
{
    private $lastMentionedFriendsIDs = [];

    public function addMentionedFriendID($element)
    {
        array_push($this->lastMentionedFriendsIDs, $element);
    }

    public function getlastMentionedFriendsIDs()
    {
        return $this->lastMentionedFriendsIDs;
    }

    public function emptyOutlastMentionedFriendsIDs()
    {
        if(!empty($this->lastMentionedFriendsIDs)){
            $this->lastMentionedFriendsIDs = [];
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    function getMentions($content)
    {
        $this->emptyOutlastMentionedFriendsIDs();
        
        $mention_regex = '/@(\w+)/'; //mention regrex to get all @texts

        $friends_ids_ment = Friendship::where('user_id', Auth::id())->pluck('friend_id');
        $friends_data_ment =  User::whereIn('id', $friends_ids_ment)->get();
        $users =[];
        $users = $friends_data_ment->map(function ($friend) {
            return [
                'id' => $friend->id,
                'name' => $friend->name,
                'type' => 'friend',
            ];
        });

        $jsonUsers = json_encode($users);

        
        if (preg_match_all($mention_regex, $content, $matches))
        {
            $ids=[];
            $foundMatches=[];
            foreach ($matches[1] as $match)
            {                
                foreach(json_decode($jsonUsers, true) as $user) {
                    if(strtolower($user['name']) == strtolower($match)) {
                        array_push($ids, $user['id']);    
                        array_push($foundMatches, $user['name']);                                      
                    }
                }
                
            }
            $counter = -1;
                foreach ($ids as $id){
                $counter++;
                $match_replace = '<a target="_blank" href="/profile/' . $id . '">' . $foundMatches[$counter] . '</a> ';
                $content = preg_replace('/@'. $foundMatches[$counter] .'/', $match_replace, $content, 1);
                $this->addMentionedFriendID($id);
            }
        }

        return $content;  
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $content = $request->input('content');
        if($content==null){
            return redirect('/posts');
        }
        $content = $this->getMentions($content);
        $post = new Post();
        $post->user_id = $user->id;
        $post->content = $content;
        $post->save();

        $friends = $this->getlastMentionedFriendsIDs();
        if(!empty($friends)){
            foreach($friends as $friend){
                $notif = new Notification();
                $notif->content = $user->name . " has mentioned you in a post.";
                $notif->type_id = $post->id;
                $notif->user_id = $user->id;
                $notif->friend_id = $friend;
                $notif->type = "post";
                $notif->save();
            }
        }
        return redirect('/posts');
    }

    public function deletePost($postId)
    {
        $post = Post::findOrFail($postId);    
        $post->delete();
        $post->comments()->delete();

        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
    
        return redirect()->back()->with('success', 'Post deleted successfully.');
    }
    public function deleteSinglePost($postId)
    {
        $post = Post::findOrFail($postId);    
        $post->delete();
        $post->comments()->delete();

        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
    
        return view('posts', ['posts' => $posts]);
    }
    public function posts(){        

        $friendshipsController = new FriendshipsController();
        $friendshipsController->refreshFriends(Auth::id());
        $friendshipsController->refreshUsersToAdd(Auth::id());
        $friendshipsController->saveFriendsJSON(Auth::id());

        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return view('posts', ['posts' => $posts]);
    }

    public function toggleLike(Request $request)
    {
        $user = auth()->user();
        $postId = $request->input('postId');
        $post = Post::find($postId);

        $existingLike = Like::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();
        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'post_id' => $postId,
            ]);
            if($user->id != $post->user_id){
                $notif = new Notification();
                $notif->content = $user->name . " has liked your post.";
                $notif->type_id = $post->id;
                $notif->user_id = $user->id;
                $notif->friend_id = $post->user_id;
                $notif->type = "likes";
                $notif->save();
            }          
            $liked = true;
        }
                
        $likeCount = $post->likes()->count();

        return response()->json(['liked' => $liked, 'likeCount' => $likeCount]);
    }

    public function getComments($postId){
        $userCommentPairs = $this->getUserCommentPairs($postId);      
        
        return response()->json(['userCommentPairs' => $userCommentPairs]);
    }

    public function getUserCommentPairs($postId){
        $comments = Comment::where('post_id', $postId)->get();
        $userCommentPairs = $comments->map(function ($comment) {
            $user = $comment->user;
            return [
                'user' => $user,
                'comment' => $comment,
            ];
        });
        return $userCommentPairs;
    }

    public function showComments($postId){
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        $postWithOpenedCommentsId = $postId;

        $userCommentPairs = $this->getUserCommentPairs($postId);
            
        return view('posts', ['posts' => $posts,'postWithOpenedCommentsId' => $postWithOpenedCommentsId, 'userCommentPairs' => $userCommentPairs]);
        
        // return view('posts', ['posts' => $posts, 'postWithOpenedCommentsId' => $postWithOpenedCommentsId, 'comments' => $comments, 'users' => $users]);
    }
    public function showCommentsInProfilePage($postId){
        
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        $postWithOpenedCommentsId = $postId;

        $post = Post::with('user')->find($postId);
        $user = $post->user();

        $userCommentPairs = $this->getUserCommentPairs($postId);
            error_log("Yes");
        // return view('profile', ['user' => $user, 'posts' => $posts,'postWithOpenedCommentsId' => $postWithOpenedCommentsId, 'userCommentPairs' => $userCommentPairs]);
        return redirect()->back()->with(['user' => $user, 'posts' => $posts,'postWithOpenedCommentsId' => $postWithOpenedCommentsId, 'userCommentPairs' => $userCommentPairs]);
        // return view('posts', ['posts' => $posts, 'postWithOpenedCommentsId' => $postWithOpenedCommentsId, 'comments' => $comments, 'users' => $users]);
    }    
    public function showCommentsInSinglePost($postId){
        // $posts = Post::with('user')->orderBy('created_at', 'desc')->get();

        $post = Post::with('user')->find($postId);

        $userCommentPairs = $this->getUserCommentPairs($postId);
        error_log($userCommentPairs);
            
        return redirect()->back()->with(['post'=> $post, 'userCommentPairs' => $userCommentPairs]);
    }

    public function postComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $comment = new Comment();
        $comment->post_id = $postId;
        $comment->user_id = $request->user()->id;
        
        $comment->content = $request->input('content');
        if($comment->content == null){
            return 0;
        }
        $comment->content = $this->getMentions($comment->content);
        $comment->save();

        $friends = $this->getlastMentionedFriendsIDs();
        if(!empty($friends)){
            foreach($friends as $friend){
                $notif = new Notification();
                $notif->content = $request->user()->name . " has mentioned you in a comment.";
                $notif->type_id = $comment->id;
                $notif->user_id = $comment->user_id;
                $notif->friend_id = $friend;
                $notif->type = "comment";
                $notif->save();
            }
        }

        $post = Post::findOrFail($postId);
        $postOwner = User::where('id', $post->user_id)->first();
        $notif = new Notification();
        $notif->content = $request->user()->name . " has comment on your post.";
        $notif->type_id = $postId;
        $notif->user_id = $comment->user_id;
        $notif->friend_id = $postOwner->id;
        $notif->type = "comment";
        $notif->save();
        return redirect()->back()->with('success', 'Comment posted successfully');
    }
    public function deleteComment($postId, $commentId)
    {
        error_log("DELETING COMMENT!!!");
        
        // Find the comment by ID
        $comment = Comment::findOrFail($commentId);
    
        // Check if the authenticated user is authorized to delete the comment
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        // Delete the comment
        $comment->delete();
    
        // You can optionally return a success response or any other data you need
        return response()->json(['message' => 'Comment deleted successfully']);
        
    }



    public function editPost($postId)
    {
        $post = Post::findOrFail($postId);
    
        return view('edit', ['post' => $post]);
    }
    public function updateSinglePost(Request $request)
    {
        $postId = $request->input('postId');
        $request->validate([
            'content' => 'required',
        ]);
        $post = Post::find($postId);
        $post->content = $request->input('content');
        $post->content = $this->getMentions($post->content);
        $post->save();
    
        return response()->json(['message' => 'Post updated successfully', 'content' => Post::find($postId)->content]);
    }
    
    public function updatePost(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
    
        $request->validate([
            'content' => 'required',
        ]);
    
        $post->content = $request->input('content');
        $post->content = $this->getMentions($post->content);
        $post->save();


        $friendshipsController = new FriendshipsController();
        $friendshipsController->refreshFriends(Auth::id());
        $friendshipsController->refreshUsersToAdd(Auth::id());

        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return redirect()->route('posts',['posts' => $posts]);
    }

    public function showPost($postId){
        $post = Post::findOrFail($postId);
        return view('post',['post' => $post]);
    }
}
