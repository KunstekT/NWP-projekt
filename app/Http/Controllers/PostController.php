<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class PostController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();
        $content = $request->input('content');
    
        $post = new Post();
        $post->user_id = $user->id;
        $post->content = $content;
        $post->save();
    
        return redirect('/wall');
    }

    public function wall(){
        $posts = Post::with('user')->get();
        return view('wall', compact('posts'));
    }
}
