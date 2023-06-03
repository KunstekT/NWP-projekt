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

Route::get('/chat', 'App\Http\Controllers\ChatController@chat')->name('chat');

//Route::post('/getChatWithAUser/{sender_id}/{receiver_id}', 'App\Http\Controllers\ChatController@getChatWithAUser')->name('getChatWithAUser');
Route::get('/getChatWithAUser/{sender_id}/{receiver_id}', 'App\Http\Controllers\ChatController@getChatWithAUser')->name('getChatWithAUser');

Route::post('/chat/send/{senderId}/{receiverId}', 'App\Http\Controllers\ChatController@send');
Route::post('/send-message/{senderId}/{receiverId}', 'App\Http\Controllers\ChatController@send')->name('send.message');

Route::get('/friends/{userId}', 'App\Http\Controllers\FriendshipsController@get')->name('friends');
Route::get('/findFriends/{userId}', 'App\Http\Controllers\FriendshipsController@findFriends')->name('findFriends');
Route::get('/addFriend/{userId}/{friendId}', 'App\Http\Controllers\FriendshipsController@addFriend')->name('addFriend');
Route::get('/removeFriend/{userId}/{friendId}', 'App\Http\Controllers\FriendshipsController@removeFriend')->name('removeFriend');

Route::post('/post/create', 'App\Http\Controllers\PostController@create')->name('post.create');
Route::get('/posts', 'App\Http\Controllers\PostController@posts')->name('posts');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/posts', [App\Http\Controllers\PostController::class, 'posts'])->name('posts');
