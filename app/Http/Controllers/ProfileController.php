<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function showProfile($userId)
    {
        $user = User::find($userId);
    
        return view('profile', compact('user'));
    }
    private function resizeImage($path, $width, $height)
    {
        $image = Image::make($path)->resize($width, $height);
        $image->save();
    }

    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Get the authenticated user
        $user = Auth::user();
    
        // Check if the uploaded image is not the default image
        $isDefaultImage = $user->profile_image === 'default.png';

        // Delete the previous profile image if it exists and it's not the default image
        if (!$isDefaultImage && $user->profile_image) {
            Storage::delete('public/profile_images/' . $user->profile_image);
        }
            
        // Store the uploaded image
        $imagePath = $request->file('profile_image')->store('public/profile_images');
    
        // Extract the filename from the image path
        $filename = basename($imagePath);
    
        // Update the user's profile image link in the database
        $user->profile_image = $filename;
        $user->save();
    
        return redirect()->back();
    }

    public function editAbout(Request $request){
        $user = Auth::user();
        
        // return view('edit', ['post' => $post]);
    }

    public function updateAbout(Request $request){
        $user = Auth::user();
        $user->about = $request->input('content');
        $user->save();

        return redirect()->back();
    }


}
