<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Like;
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
    public function index()
    {
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return view('posts', ['posts' => $posts]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $content = $request->input('content');
    
        $post = new Post();
        $post->user_id = $user->id;
        $post->content = $content;
        $post->save();
    
        return redirect('/posts');
    }

    public function deletePost($postId)
{
    $post = Post::findOrFail($postId);

    // Delete the post
    $post->delete();

    // Optionally, delete associated comments
    $post->comments()->delete();

    $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
    return redirect()->route('posts')->with('success', 'Post deleted successfully.');
}
    public function posts(){        

        $friendshipsController = new FriendshipsController();
        $friendshipsController->refreshFriends(Auth::id());
        $friendshipsController->refreshUsersToAdd(Auth::id());

        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return view('posts', ['posts' => $posts]);
    }

    public function toggleLike(Request $request, $postId)
    {
        $user = $request->user(); // Retrieve the authenticated user
        // $post = $request->post();
        // $post = Post::findOrFail($postId); // Retrieve the post

        // Check if the user has already liked the post
        $existingLike = Like::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();

        if ($existingLike) {
            // User already liked the post, so unlike it
            $existingLike->delete();
            $liked = false;
        } else {
            // User hasn't liked the post, so create a new like
            Like::create([
                'user_id' => $user->id,
                'post_id' => $postId,
            ]);
            $liked = true;
        }

        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        // return response()->json(['liked' => $liked]);
        return view('posts', ['posts' => $posts]);
    }

    public function showComments($postId){
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        $postWithOpenedCommentsId = $postId;
        $comments = Comment::where('post_id', $postId)->get();
        
        return view('posts', ['posts' => $posts, 'postWithOpenedCommentsId' => $postWithOpenedCommentsId, 'comments' => $comments]);
    }
}
