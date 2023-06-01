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

Route::get('/', function () {
    return view('home');
});

Route::get('/home', 'App\Http\Controllers\HomeController@showHomePage')->name('home');

Route::get('/chat', 'App\Http\Controllers\ChatController@index')->name('chat');
Route::post('/chat/send', 'App\Http\Controllers\ChatController@send');

Route::get('/login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login');
Route::post('/logout', 'App\Http\Controllers\Auth\LogoutController@logout')->name('logout');

Route::get('/register', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/register', 'App\Http\Controllers\Auth\RegisterController@register');