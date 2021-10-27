<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $primaryKey='id_transaction';
    use SoftDeletes;
}
