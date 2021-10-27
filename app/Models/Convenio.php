<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Convenio extends Model
{
    protected $table = 'convenios';
    protected $primaryKey='convenio_id';
    use SoftDeletes;
}
