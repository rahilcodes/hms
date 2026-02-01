<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth('admin')->user();

        // Super Admin has access to everything
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Check if the user has any of the required roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Default: Access Denied
        return response()->view('errors.403', [
            'message' => 'Your account (' . strtoupper($user->role) . ') does not have access to this operational module.'
        ], 403);
    }
}
