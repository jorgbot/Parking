<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey='id_product';

    use SoftDeletes;
}
