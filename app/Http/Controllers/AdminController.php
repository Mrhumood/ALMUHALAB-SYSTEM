<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FollowUp;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // ── Users ──────────────────────────────────────────────

    public function users()
    {
        $users = User::with('role')
            ->withCount('serviceRequests')
            ->orderBy('name')
            ->paginate(20);

        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|unique:users,email,' . $user->id,
            'password'         => 'nullable|string|min:8|confirmed',
            'phone_number'     => 'nullable|string|max:30',
            'whatsapp_number'  => 'nullable|string|max:30',
            'notify_email'     => 'nullable|boolean',
            'notify_whatsapp'  => 'nullable|boolean',
        ]);

        $user->name             = $data['name'];
        $user->email            = $data['email'];
        $user->phone_number     = $data['phone_number'] ?? null;
        $user->whatsapp_number  = $data['whatsapp_number'] ?? null;
        $user->notify_email     = (bool) ($data['notify_email'] ?? false);
        $user->notify_whatsapp  = (bool) ($data['notify_whatsapp'] ?? false);
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return back()->with('success', "User \"{$user->name}\" updated.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "User \"{$name}\" deleted.");
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'nullable|exists:roles,id',
        ]);

        // Prevent admin from removing their own admin role
        if ($user->id === auth()->id() && $request->role_id != $user->role_id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role_id' => $request->role_id ?: null]);

        return back()->with('success', "Role updated for {$user->name}.");
    }

    // ── Roles & Permissions ────────────────────────────────

    public function roles()
    {
        $roles = Role::with('permissions')->withCount('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return back()->with('success', "Permissions updated for role \"{$role->name}\".");
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
        ]);

        Role::create(['name' => ucfirst(trim($request->name))]);

        return back()->with('success', "Role \"{$request->name}\" created successfully.");
    }

    public function destroyRole(Role $role)
    {
        $userCount = User::where('role_id', $role->id)->count();

        if ($userCount > 0) {
            return back()->with('error', "Cannot delete \"{$role->name}\" — {$userCount} " . Str::plural('user', $userCount) . " still assigned to it.");
        }

        $role->permissions()->detach();
        $role->delete();

        return back()->with('success', "Role \"{$role->name}\" deleted.");
    }

    // ── Audit Log ──────────────────────────────────────────

    public function auditLog(Request $request)
    {
        $query = ActivityLog::with([])
            ->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%");
            });
        }

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        $logs  = $query->paginate(30)->withQueryString();
        $users = User::orderBy('name')->get()->keyBy('id');

        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit_log.index', compact('logs', 'users', 'actions'));
    }

    public function auditLogForRequest(ServiceRequest $serviceRequest)
    {
        // All activity on the SR itself + all follow-up activity linked to it
        $followUpIds = $serviceRequest->followUps()->withoutGlobalScopes()->pluck('id');

        $logs = ActivityLog::where(function ($q) use ($serviceRequest, $followUpIds) {
                    $q->where('subject_type', ServiceRequest::class)
                      ->where('subject_id', $serviceRequest->id);
                })->orWhere(function ($q) use ($followUpIds) {
                    $q->where('subject_type', FollowUp::class)
                      ->whereIn('subject_id', $followUpIds);
                })
                ->orderBy('created_at', 'desc')
                ->get();

        $users = User::orderBy('name')->get()->keyBy('id');

        return view('admin.audit_log.show', compact('serviceRequest', 'logs', 'users'));
    }
}
