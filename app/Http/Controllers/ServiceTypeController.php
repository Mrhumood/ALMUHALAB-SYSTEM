<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $types = ServiceType::withCount('serviceRequests')->orderBy('name')->get();
        return view('admin.service_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:service_types,name',
            'description' => 'nullable|string|max:255',
        ]);

        ServiceType::create([
            'name'        => trim($request->name),
            'description' => trim($request->description ?? ''),
        ]);

        return back()->with('success', "Service type \"{$request->name}\" created.");
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:service_types,name,' . $serviceType->id,
            'description' => 'nullable|string|max:255',
            'is_active'   => 'boolean',
        ]);

        $serviceType->update([
            'name'        => trim($request->name),
            'description' => trim($request->description ?? ''),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Service type updated.");
    }

    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->serviceRequests()->count() > 0) {
            return back()->with('error', "Cannot delete \"{$serviceType->name}\" — it has linked requests.");
        }

        $serviceType->delete();
        return back()->with('success', "Service type \"{$serviceType->name}\" deleted.");
    }
}
