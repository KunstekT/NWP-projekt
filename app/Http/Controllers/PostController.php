<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

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

        $posts = Post::with('user')->get();
        return view('posts', compact('posts'));
    }
}
