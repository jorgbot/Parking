<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Prestamo extends Model
{
    protected $table = 'prestamo';
    protected $primaryKey='id_prestamo';

    use SoftDeletes;
}
