<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{

    protected $table = 'notifications';

    protected $fillable = [
        'notification_for_id', 'notification_from_id', 'target', 'type', 'status'
    ];

    public function user() { return $this->belongsTo('App\User'); }

    public static function getNotifications($id) 
    {
        return DB::table('notifications AS n')
            ->leftJoin('posts as p', 'n.target', '=' ,'p.id')
            ->leftJoin('users as u', 'n.notification_from_id', '=' ,'u.id')
            ->where('n.notification_for_id', $id)
            ->where('n.status', 0)
            ->selectRaw('u.id AS user_id, u.username, u.profile_image, p.id as post_id, p.image, n.id as notificationId, n.type, n.created_at')
            ->get();    	
    }

}
