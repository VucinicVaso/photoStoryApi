<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;

//events, models
use App\Events\NotificationEvent;
use App\Notification;

class NotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotificationEvent  $event
     * @return void
     */
    public function handle(NotificationEvent $event)
    {
        Notification::create([
            'notification_for_id'  => $event->post->user_id,        
            'notification_from_id' => Auth::user()->id,
            'target'               => $event->post->id,
            'type'                 => $event->type,
            'status'               => 0
        ]);
    }
}
