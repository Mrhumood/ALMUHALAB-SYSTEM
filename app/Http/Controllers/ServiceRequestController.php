<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\ActivityLog;
use App\Http\Requests\ServiceRequestRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $items = ServiceRequest::orderBy('created_at', 'desc')->get();
        return view('service_requests.index', ['items' => $items]);
    }

    public function create()
    {
        return view('service_requests.create');
    }

    public function store(ServiceRequestRequest $request)
    {
        $data = $request->validatedPayload();

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('service_requests', 'public');
            $data['attachment_path'] = $path;
        }

        $sr = ServiceRequest::create($data);

        ActivityLog::create([
            'user' => auth()->check() ? auth()->user()->id : null,
            'action' => 'created',
            'subject_type' => ServiceRequest::class,
            'subject_id' => $sr->id,
            'changes' => $sr->toArray(),
        ]);

        return redirect()->route('service-requests.show', $sr)->with('success', 'Service request created.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        return view('service_requests.show', ['serviceRequest' => $serviceRequest]);
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        return view('service_requests.edit', ['serviceRequest' => $serviceRequest]);
    }

    public function update(ServiceRequestRequest $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validatedPayload();

        if ($request->hasFile('attachment')) {
            // delete old file if exists
            if ($serviceRequest->attachment_path) {
                try { \Storage::disk('public')->delete($serviceRequest->attachment_path); } catch (\Exception $e) {}
            }
            $path = $request->file('attachment')->store('service_requests', 'public');
            $data['attachment_path'] = $path;
        }

        $original = $serviceRequest->getOriginal();
        $serviceRequest->update($data);

        ActivityLog::create([
            'user' => auth()->check() ? auth()->user()->id : null,
            'action' => 'updated',
            'subject_type' => ServiceRequest::class,
            'subject_id' => $serviceRequest->id,
            'changes' => [
                'before' => $original,
                'after' => $serviceRequest->toArray(),
            ],
        ]);

        return redirect()->route('service-requests.show', $serviceRequest)->with('success', 'Updated.');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        // perform a soft delete (move to trash). Do NOT remove attachment files here so they can be restored.
        $data = $serviceRequest->toArray();

        $serviceRequest->delete();

        ActivityLog::create([
            'user' => auth()->check() ? auth()->user()->id : null,
            'action' => 'deleted',
            'subject_type' => ServiceRequest::class,
            'subject_id' => $serviceRequest->id,
            'changes' => $data,
        ]);

        return redirect()->route('service-requests.index')->with('success', 'Moved to trash.');
    }

    // show trashed (soft-deleted) service requests
    public function trash()
    {
        $items = ServiceRequest::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('service_requests.trash', ['items' => $items]);
    }

    // restore a trashed request
    public function restore($id)
    {
        $sr = ServiceRequest::onlyTrashed()->findOrFail($id);
        $sr->restore();

        ActivityLog::create([
            'user' => auth()->check() ? auth()->user()->id : null,
            'action' => 'restored',
            'subject_type' => ServiceRequest::class,
            'subject_id' => $sr->id,
            'changes' => $sr->toArray(),
        ]);

        return redirect()->route('service-requests.trash')->with('success', 'Restored.');
    }

    // permanently delete a trashed request
    public function forceDelete($id)
    {
        $sr = ServiceRequest::onlyTrashed()->findOrFail($id);
        $data = $sr->toArray();

        // delete attached file if exists
        if ($sr->attachment_path) {
            try { \Storage::disk('public')->delete($sr->attachment_path); } catch (\Exception $e) {}
        }

        $sr->forceDelete();

        ActivityLog::create([
            'user' => auth()->check() ? auth()->user()->id : null,
            'action' => 'permanently_deleted',
            'subject_type' => ServiceRequest::class,
            'subject_id' => $id,
            'changes' => $data,
        ]);

        return redirect()->route('service-requests.trash')->with('success', 'Permanently deleted.');
    }
}
