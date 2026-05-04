<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\ServiceCatalog;
use App\Models\StageServiceMapping;
use Illuminate\Http\Request;

class ServiceCatalogAdminController extends Controller
{
    public function index()
    {
        $services   = ServiceCatalog::withCount('requestServices')->orderBy('name')->get();
        $stageTypes = FollowUp::STATUS_TYPES;

        return view('admin.service_catalog.index', compact('services', 'stageTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:50',
            'color'       => 'nullable|string|in:primary,success,info,warning,danger,secondary,dark',
        ]);

        $data['icon']  = $data['icon']  ?? 'bi-star';
        $data['color'] = $data['color'] ?? 'primary';

        ServiceCatalog::create($data);

        return back()->with('success', "Service \"{$data['name']}\" created.");
    }

    public function update(Request $request, ServiceCatalog $serviceCatalog)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:50',
            'color'       => 'nullable|string|in:primary,success,info,warning,danger,secondary,dark',
            'is_active'   => 'boolean',
            'stages'      => 'nullable|array',
            'stages.*'    => 'string',
        ]);

        $serviceCatalog->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'icon'        => $data['icon']  ?? 'bi-star',
            'color'       => $data['color'] ?? 'primary',
            'is_active'   => $request->boolean('is_active', true),
        ]);

        // Sync stage mappings
        $stages = $data['stages'] ?? [];
        $validStages = array_keys(FollowUp::STATUS_TYPES);

        StageServiceMapping::where('service_catalog_id', $serviceCatalog->id)->delete();

        foreach (array_intersect($stages, $validStages) as $stage) {
            StageServiceMapping::create([
                'service_catalog_id' => $serviceCatalog->id,
                'status_type'        => $stage,
            ]);
        }

        return back()->with('success', "Service \"{$serviceCatalog->name}\" updated.");
    }

    public function destroy(ServiceCatalog $serviceCatalog)
    {
        if ($serviceCatalog->requestServices()->count() > 0) {
            return back()->with('error', "Cannot delete \"{$serviceCatalog->name}\" — it has active request services.");
        }

        $name = $serviceCatalog->name;
        $serviceCatalog->stageMappings()->delete();
        $serviceCatalog->delete();

        return back()->with('success', "Service \"{$name}\" deleted.");
    }
}
