<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Like;

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
        return view('posts');
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

    public function posts(){        

        $friendshipsController = new FriendshipsController();
        $friendshipsController->refreshFriends(Auth::id());
        $friendshipsController->refreshUsersToAdd(Auth::id());

        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return view('posts', compact('posts'));
    }

    public function toggleLike(Request $request, $postId)
{
    $user = $request->user(); // Retrieve the authenticated user
    $post = Post::findOrFail($postId); // Retrieve the post

    // Check if the user has already liked the post
    $existingLike = Like::where('user_id', $user->id)
        ->where('post_id', $post->id)
        ->first();

    if ($existingLike) {
        // User already liked the post, so unlike it
        $existingLike->delete();
        $liked = false;
    } else {
        // User hasn't liked the post, so create a new like
        Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
        $liked = true;
    }

    return response()->json(['liked' => $liked]);
}
}
