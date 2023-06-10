<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Like;
use App\Models\User;
use App\Models\Comment;

class PostController extends Controller
{
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
        $json = 'users.json';
        $data = file_get_contents($json);
        $users = json_decode($data, true);

        $mention_regex = '/@(\w+)/'; //mention regrex to get all @texts
        
        if (preg_match_all($mention_regex, $content, $matches))
        {
            foreach ($matches[1] as $match)
            {
                $id=0;
                foreach($users as $user) {
                    if(strtolower($user['name']) === strtolower($match)) {
                        $id = $user['id'];                    
                    }
                }
                if($id != 0){
                    $match_replace = '<a target="_blank" href="/profile/' . $id . '">' . $match . '</a>';
                    $content = str_replace("@" . $match, $match_replace, $content);
                }
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
        $comment->save();

        return redirect()->back()->with('success', 'Comment posted successfully');
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
        $post->save();


        $friendshipsController = new FriendshipsController();
        $friendshipsController->refreshFriends(Auth::id());
        $friendshipsController->refreshUsersToAdd(Auth::id());

        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return redirect()->route('posts',['posts' => $posts]);
    }
}
