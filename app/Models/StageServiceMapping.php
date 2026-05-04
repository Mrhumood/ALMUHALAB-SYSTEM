<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageServiceMapping extends Model
{
    protected $fillable = ['status_type', 'service_catalog_id'];

    public function service()
    {
        return $this->belongsTo(ServiceCatalog::class, 'service_catalog_id');
    }
}
