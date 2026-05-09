<?php

namespace App\Http\Controllers;

use App\Models\ServiceCatalog;
use App\Models\StageServiceMapping;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class ServiceCatalogAdminController extends Controller
{
    private function stageTypes(): array
    {
        $result = [];
        foreach (WorkflowService::STAGES as $cfg) {
            $result[$cfg['key']] = $cfg;
        }
        return $result;
    }

    public function index()
    {
        $services   = ServiceCatalog::withCount('requestServices')->orderBy('name')->get();
        $stageTypes = $this->stageTypes();

        return view('admin.service_catalog.index', compact('services', 'stageTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:50',
            'color'       => 'nullable|string|in:primary,success,info,warning,danger,secondary,dark',
            'stages'      => 'nullable|array',
            'stages.*'    => 'string',
        ]);

        $service = ServiceCatalog::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'icon'        => $data['icon']  ?? 'bi-star',
            'color'       => $data['color'] ?? 'primary',
        ]);

        $validStages = array_keys($this->stageTypes());
        foreach (array_intersect($data['stages'] ?? [], $validStages) as $stage) {
            StageServiceMapping::create([
                'service_catalog_id' => $service->id,
                'status_type'        => $stage,
            ]);
        }

        return back()->with('success', "Service \"{$service->name}\" created.");
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

        $validStages = array_keys($this->stageTypes());
        StageServiceMapping::where('service_catalog_id', $serviceCatalog->id)->delete();
        foreach (array_intersect($data['stages'] ?? [], $validStages) as $stage) {
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
