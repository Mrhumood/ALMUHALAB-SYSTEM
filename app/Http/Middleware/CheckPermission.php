<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Usage: 'permission:view_requests'
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if ($user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Forbidden');
    }
}
