<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    function index(Request $request)
    {
        //
    }

    function read(Request $request)
    {
        $notification = Notification::where('id', $request->id)->first();
        if (empty($notification)) {
            return response()->json([
                'success' => false
            ]);
        }
        $notification->markAsRead();
        return response()->json([
            'success' => true,
            'redirect' => isset($notification->data['url']) ? $notification->data['url'] : ''
        ]);
    }
}
