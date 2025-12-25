<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class shipping extends Model
{
    protected $fillable = ['title','description','price'];
    protected $table = 'shipping';
}
