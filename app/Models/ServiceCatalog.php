<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCatalog extends Model
{
    protected $table = 'service_catalog';

    protected $fillable = ['name', 'description', 'icon', 'color', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    const COLORS = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark'];

    public function stageMappings()
    {
        return $this->hasMany(StageServiceMapping::class);
    }

    public function requestServices()
    {
        return $this->hasMany(RequestService::class);
    }

    public function mappedStages(): array
    {
        return $this->stageMappings()->pluck('status_type')->toArray();
    }
}
