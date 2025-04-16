<?php

namespace App\Http\Controllers;
use App\Models\Notifications; 
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //
    public function showNotifications()
    {
       
        // Return the view and pass the notifications data to the view
        return view('notifications.index');
    }

    public function markNotificationsAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->route('notifications.index');
    }
}
