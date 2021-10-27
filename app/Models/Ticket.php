<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Ticket extends Model
{
    use SoftDeletes;
    use Notifiable;
    protected $table = 'tickets';
    protected $primaryKey='ticket_id';
    protected $dates = ['deleted_at'];

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }
}
