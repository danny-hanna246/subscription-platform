<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class NotificationController extends Controller
{
    public function index()
    {
        /** @var Admin $admin */
        $admin = auth('admin')->user();

        $notifications = $admin
            ->notifications()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        /** @var Admin $admin */
        $admin = auth('admin')->user();

        $notification = $admin
            ->notifications()
            ->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        /** @var Admin $admin */
        $admin = auth('admin')->user();

        $admin
            ->unreadNotifications
            ->markAsRead();

        return redirect()->back();
    }
}
