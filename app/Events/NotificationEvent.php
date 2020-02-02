<?php

namespace App\Events;

class NotificationEvent extends Event
{

    public $post; 
    public $type;
 
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($post, $type)
    {
        $this->post = $post;
        $this->type = $type;
    }

}