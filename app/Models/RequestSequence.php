<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestSequence extends Model
{
    protected $fillable = ['prefix', 'year', 'last_number'];
}
