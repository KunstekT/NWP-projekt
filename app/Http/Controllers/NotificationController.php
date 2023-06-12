<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function receiveNotifications($receiverId)
    { 
        $notifications = Notification::orderByDesc('created_at')->get();
        $notificationsHtml = view('partials.notifications_list', ['notifications' => $notifications])->render();
        return response()->json(['notificationsHtml'=> $notificationsHtml ]);
    }
}
