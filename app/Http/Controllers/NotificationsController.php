<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

// models
use App\User;
use App\Notification;

class NotificationsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /* get notifications */
    public function index()
    {
        $notifications = Notification::getNotifications(Auth::user()->id);
        Notification::where('notification_for_id', Auth::user()->id)->where('status', 0)->update(['status' => 1]);

        return !empty($data) && count($data) > 0 
            ? response()->json(["notifications" => $notifications], 200)
            : response()->json(["errors" => '0 new notifications!'], 400);
    }

}