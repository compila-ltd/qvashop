<?php

namespace App\Http\Controllers\Seller;

use Auth;

class NotificationController extends Controller
{
    public function index() {
        $notifications = auth()->user()->notifications()->paginate(15);
        auth()->user()->unreadNotifications->markAsRead();
        
        return view('seller.notification.index', compact('notifications'));
    }
}
