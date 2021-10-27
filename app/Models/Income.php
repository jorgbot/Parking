<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Income extends Model
{
    protected $table = 'incomes';
    protected $primaryKey='id_income';
    use SoftDeletes;
}
