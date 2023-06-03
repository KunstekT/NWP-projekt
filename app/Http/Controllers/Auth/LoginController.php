<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FriendshipsController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        // Custom logic to perform after successful login
        $friendshipsController = new FriendshipsController();
        $friendshipsController->refreshFriends($user->id);
        $friendshipsController->refreshUsersToAdd($user->id);

        return redirect('/wall');
    }
}