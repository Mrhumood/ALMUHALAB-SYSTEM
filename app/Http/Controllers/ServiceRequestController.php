<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Models\ActivityLog;
use App\Http\Requests\ServiceRequestRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->hasPermission('edit_request');

        $query = ServiceRequest::orderBy('created_at', 'desc');

        // Regular users see only their own requests
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $items = $query->paginate(15);

        $statsQuery = $isAdmin
            ? ServiceRequest::query()
            : ServiceRequest::where('user_id', $user->id);

        $stats = [
            'total'        => (clone $statsQuery)->count(),
            'new'          => (clone $statsQuery)->where('status', 'New')->count(),
            'under_review' => (clone $statsQuery)->where('status', 'Under Review')->count(),
            'approved'     => (clone $statsQuery)->where('status', 'Approved')->count(),
            'completed'    => (clone $statsQuery)->where('status', 'Completed')->count(),
        ];

        return view('service_requests.index', compact('items', 'stats', 'isAdmin'));
    }

    public function create()
    {
        $serviceTypes = ServiceType::where('is_active', true)->orderBy('name')->get();
        return view('service_requests.create', compact('serviceTypes'));
    }

    public function store(ServiceRequestRequest $request)
    {
        $data = $request->validatedPayload();
        $data['user_id'] = auth()->id();

        $sr = ServiceRequest::create($data);

        // Store multiple attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('service_requests', 'public');
                $sr->attachments()->create([
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        ActivityLog::create([
            'user'         => auth()->id(),
            'action'       => 'created',
            'subject_type' => ServiceRequest::class,
            'subject_id'   => $sr->id,
            'changes'      => $sr->toArray(),
        ]);

        return redirect()->route('service-requests.show', $sr)->with('success', 'Request created successfully.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $this->authorizeAccess($serviceRequest);
        return view('service_requests.show', compact('serviceRequest'));
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        $this->authorizeAccess($serviceRequest);
        $serviceTypes = ServiceType::where('is_active', true)->orderBy('name')->get();
        return view('service_requests.edit', compact('serviceRequest', 'serviceTypes'));
    }

    public function update(ServiceRequestRequest $request, ServiceRequest $serviceRequest)
    {
        $this->authorizeAccess($serviceRequest);

        $data = $request->validatedPayload();

        // Append new attachments (keep existing ones)
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('service_requests', 'public');
                $serviceRequest->attachments()->create([
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        $original = $serviceRequest->getOriginal();
        $serviceRequest->update($data);

        ActivityLog::create([
            'user'         => auth()->id(),
            'action'       => 'updated',
            'subject_type' => ServiceRequest::class,
            'subject_id'   => $serviceRequest->id,
            'changes'      => ['before' => $original, 'after' => $serviceRequest->toArray()],
        ]);

        return redirect()->route('service-requests.show', $serviceRequest)->with('success', 'Request updated.');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        $this->authorizeAccess($serviceRequest);

        $data = $serviceRequest->toArray();
        $serviceRequest->delete();

        ActivityLog::create([
            'user'         => auth()->id(),
            'action'       => 'deleted',
            'subject_type' => ServiceRequest::class,
            'subject_id'   => $serviceRequest->id,
            'changes'      => $data,
        ]);

        return redirect()->route('service-requests.index')->with('success', 'Request moved to trash.');
    }

    public function trash()
    {
        $items = ServiceRequest::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('service_requests.trash', compact('items'));
    }

    public function restore($id)
    {
        $sr = ServiceRequest::onlyTrashed()->findOrFail($id);
        $sr->restore();

        ActivityLog::create([
            'user'         => auth()->id(),
            'action'       => 'restored',
            'subject_type' => ServiceRequest::class,
            'subject_id'   => $sr->id,
            'changes'      => $sr->toArray(),
        ]);

        return redirect()->route('service-requests.trash')->with('success', 'Request restored.');
    }

    public function forceDelete($id)
    {
        $sr = ServiceRequest::onlyTrashed()->findOrFail($id);
        $data = $sr->toArray();

        // Delete legacy single attachment
        if ($sr->attachment_path) {
            try { \Storage::disk('public')->delete($sr->attachment_path); } catch (\Exception $e) {}
        }

        // Delete all attachments from attachments table
        foreach ($sr->attachments as $attachment) {
            try { \Storage::disk('public')->delete($attachment->file_path); } catch (\Exception $e) {}
        }

        $sr->forceDelete();

        ActivityLog::create([
            'user'         => auth()->id(),
            'action'       => 'permanently_deleted',
            'subject_type' => ServiceRequest::class,
            'subject_id'   => $id,
            'changes'      => $data,
        ]);

        return redirect()->route('service-requests.trash')->with('success', 'Request permanently deleted.');
    }

    public function showTrashed($id)
    {
        $serviceRequest = ServiceRequest::withTrashed()->findOrFail($id);
        return view('service_requests.show_trashed', compact('serviceRequest'));
    }

    public function deleteAttachment(ServiceRequest $serviceRequest, Attachment $attachment)
    {
        $this->authorizeAccess($serviceRequest);

        try { \Storage::disk('public')->delete($attachment->file_path); } catch (\Exception $e) {}
        $attachment->delete();

        return back()->with('success', 'Attachment removed.');
    }

    // Ensure non-admin users can only access their own requests
    private function authorizeAccess(ServiceRequest $serviceRequest): void
    {
        $user = auth()->user();
        if (!$user->hasPermission('edit_request') && $serviceRequest->user_id !== $user->id) {
            abort(403, 'You do not have access to this request.');
        }
    }
}
