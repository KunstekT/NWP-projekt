<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use app\Http\Controllers\Auth\LoginController;
use app\Http\Controllers\Auth\RegisterController;
use app\Http\Controllers\Auth\LogoutController;
use app\Http\Controllers\FriendshipsController;
use app\Http\Controllers\ChatController;
use app\Http\Controllers\PostController;

Route::get('/', function () {
    return view('home');
});

Route::get('/home', 'App\Http\Controllers\HomeController@showHomePage')->name('home');

Route::get('/login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login');
Route::post('/logout', 'App\Http\Controllers\Auth\LogoutController@logout')->name('logout');

Route::get('/register', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/register', 'App\Http\Controllers\Auth\RegisterController@register');


//Route::post('/getChatWithAUser/{sender_id}/{receiver_id}', 'App\Http\Controllers\ChatController@getChatWithAUser')->name('getChatWithAUser');
Route::get('/chat', 'App\Http\Controllers\ChatController@chat')->name('chat');
Route::get('/chat/{receiverId}', 'App\Http\Controllers\ChatController@getChatWithAUser')->name('chatWith');

// Route::post('/chat/send/{senderId}/{receiverId}', 'App\Http\Controllers\ChatController@send');
// Route::post('/send-message/{receiverId}', 'App\Http\Controllers\ChatController@send')->name('send.message');
Route::post('/send', 'App\Http\Controllers\ChatController@send')->name('send.message');
Route::get('/receive', 'App\Http\Controllers\ChatController@receive')->name('receive.message');
// Route::post('/send-message', 'App\Http\Controllers\ChatController@sendMessage')->name('send.message');
Route::get('/get-messages','App\Http\Controllers\ChatController@getMessages')->name('get.messages');

Route::get('/friends/{userId}', 'App\Http\Controllers\FriendshipsController@get')->name('friends');
Route::get('/findFriends/{userId}', 'App\Http\Controllers\FriendshipsController@findFriends')->name('findFriends');
Route::get('/addFriend/{userId}/{friendId}', 'App\Http\Controllers\FriendshipsController@addFriend')->name('addFriend');
Route::get('/acceptFriend/{userId}/{friendId}', 'App\Http\Controllers\FriendshipsController@acceptFriend')->name('acceptFriend');
Route::get('/rejectFriend/{userId}/{friendId}', 'App\Http\Controllers\FriendshipsController@rejectFriend')->name('rejectFriend');
Route::get('/removeFriend/{userId}/{friendId}', 'App\Http\Controllers\FriendshipsController@removeFriend')->name('removeFriend');

Route::post('/post/create', 'App\Http\Controllers\PostController@create')->name('post.create');
Route::get('/posts', 'App\Http\Controllers\PostController@posts')->name('posts');


Auth::routes();

Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
Route::get('/posts', 'App\Http\Controllers\PostController@posts')->name('posts');
Route::post('/like', 'App\Http\Controllers\PostController@toggleLike')->name('like');
Route::get('/comments/{postId}', 'App\Http\Controllers\PostController@getComments')->name('like');

Route::post('/posts/{postId}/comment', 'App\Http\Controllers\PostController@postComment')->name('postComment');
Route::get('/posts/{postId}/showComments', 'App\Http\Controllers\PostController@showComments')->name('showComments');
Route::get('/profile/{postId}/showComments', 'App\Http\Controllers\PostController@showCommentsInProfilePage')->name('showCommentsInProfilePage');
Route::get('/post/{postId}/showComments', 'App\Http\Controllers\PostController@showCommentsInSinglePost')->name('showCommentsInSinglePost');
// Route::delete('/posts/{postId}/showComments/{commentId}/delete', 'App\Http\Controllers\CommentController@delete')->name('comments.delete');
Route::delete('/posts/{postId}/showComments/{commentId}/delete', 'App\Http\Controllers\PostController@deleteComment')->name('comments.delete');

Route::delete('/posts/{postId}', 'App\Http\Controllers\PostController@deletePost')->name('posts.delete');
Route::delete('/posts/post/{postId}', 'App\Http\Controllers\PostController@deleteSinglePost')->name('posts.deleteSingle');
Route::get('/posts/{postId}/edit', 'App\Http\Controllers\PostController@editPost')->name('posts.edit');
Route::get('/edit/{postId}', 'App\Http\Controllers\PostController@edit')->name('post.edit');
Route::patch('/updatePost', 'App\Http\Controllers\PostController@updateSinglePost')->name('updateSinglePost');
Route::post('/posts/{postId}/update', 'App\Http\Controllers\PostController@updatePost')->name('updatePost');

Route::get('/profile/{userId}', 'App\Http\Controllers\ProfileController@showProfile')->name('profile');
Route::post('/profile/uploadProfileImage', 'App\Http\Controllers\ProfileController@uploadProfileImage')->name('profile.uploadProfileImage');
Route::post('/profile/updateAbout', 'App\Http\Controllers\ProfileController@updateAbout')->name('updateAbout');

Route::get('/post/{post}', 'App\Http\Controllers\PostController@showPost')->name('post');
Route::get('/receiveNotifications/{receiverId}', 'App\Http\Controllers\NotificationController@receiveNotifications')->name('receiveNotifications');

Route::any('{url}', function(){
    return redirect('/posts');
})->where('url', '.*');