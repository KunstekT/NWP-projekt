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

    /*public function replaceUserNames($body)
    {/*
        if($user_ids != null){
            preg_match_all('/@(\w+)/', $body, $users);

            foreach($user_ids as $user_id){
                $linkedUserList = User::find($user_id);
                $body = str_replace('@' . $linkedUserList->name, 
                    '<a href="/profile/' . $linkedUserList->id . '</a>', $body);
            }
            return $body;
        }
        else return $body;

        preg_match_all('/@(\w+)/', $body, $users);
        if (!isSet($users[1])) {
            return $body;
        }
        $userList = Users::select('select name from users');
        foreach ($userList as $user) {
            $replace = $user->name;
                if($user->id !== auth()->id()) {
                    $replace = $user->id;
                }
                $body = str_replace('@' . $user->name, '<a href="/profile/' . $replace . '">@' . $user->name . '</a>', $body);
        }
        return $body;
    }*/

    function getMentions($content)
    {
        $json = 'users.json';
        $array = json_decode($json, true);

        $mention_regex = '/@(\w+)/'; //mention regrex to get all @texts

        if (preg_match_all($mention_regex, $content, $matches))
        {
            foreach ($matches[1] as $match)
            {/*
                foreach($array AS $index => $element) {
                    if($element['name'] == $match) {
                        $id = $element['id'];
                    }
                }*/

                $match_search = '@[' . $match . ']';
                $match_replace = '<a target="_blank" href="/profile/' . $id . '">@' . $match_user['user_name'] . '</a>';

                $content = str_replace('Tester4', "http://test.com/test/", $content);
            }
        }
        return $content;    
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $content = $request->input('content');
       // $content = $this->replaceUserNames($content);
        $content = $this->getMentions($content);
        $content = preg_replace('/(\w+)/', htmlspecialchars("<a href='/profile/4'> Test</a>"), $content);
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

    public function toggleLike(Request $request, $postId)
    {
        $user = $request->user();
        // $post = $request->post();
        // $post = Post::findOrFail($postId);

        // Check if the user has already liked the post
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

        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return view('posts', ['posts' => $posts]);
    }

    public function showComments($postId){
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        $postWithOpenedCommentsId = $postId;
        $comments = Comment::where('post_id', $postId)->get();

        // $userIds = $comments->pluck('user_id')->unique();
        // $users = User::whereIn('id', $userIds)->get();
        
        $userCommentPairs = $comments->map(function ($comment) {
            $user = $comment->user;
            return [
                'user' => $user,
                'comment' => $comment,
            ];
        });
        
        return view('posts', ['posts' => $posts,'postWithOpenedCommentsId' => $postWithOpenedCommentsId, 'userCommentPairs' => $userCommentPairs]);
        
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
        return view('posts', ['posts' => $posts])->with('success', 'Post updated successfully.');
    }
}
