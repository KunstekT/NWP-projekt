<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Like;
use App\Models\User;
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
    //     return view('posts', ['posts' => $posts]);
    // }

    function getMentions($content)
    {
        $this->emptyOutlastMentionedFriendsIDs();
        $json = '/users.json';
        $data = Storage::disk('local')->get($json);
        $users = json_decode($data, true);

        $mention_regex = '/@(\w+)/'; //mention regrex to get all @texts
        
        if (preg_match_all($mention_regex, $content, $matches))
        {
            $ids=[];
            $foundMatches=[];
            foreach ($matches[1] as $match)
            {                
                foreach($users as $user) {
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
    return redirect()->route('posts')->with('success', 'Post deleted successfully.');
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
            $liked = true;
        }
                
        $likeCount = $post->likes()->count();

        return response()->json(['liked' => $liked, 'likeCount' => $likeCount]);
    }

    private function getUserCommentPairs($postId){
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
        $user = $post->user;

        $userCommentPairs = $this->getUserCommentPairs($postId);
            
        return view('profile', ['user' => $user, 'posts' => $posts,'postWithOpenedCommentsId' => $postWithOpenedCommentsId, 'userCommentPairs' => $userCommentPairs]);
        
        // return view('posts', ['posts' => $posts, 'postWithOpenedCommentsId' => $postWithOpenedCommentsId, 'comments' => $comments, 'users' => $users]);
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
}
