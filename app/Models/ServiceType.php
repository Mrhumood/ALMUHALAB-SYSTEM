<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
