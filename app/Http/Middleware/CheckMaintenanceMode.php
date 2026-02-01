<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if maintenance mode is enabled in settings
        if (site('maintenance_mode')) {

            // 2. Define exclusions (Routes that should still work)
            // - Admin panel
            // - Login/Logout
            // - Asset files (though usually handled by web server, good to be safe)
            if ($request->is('admin*') || $request->is('login') || $request->is('logout')) {
                return $next($request);
            }

            // 3. Return 503 Service Unavailable
            abort(503, 'We are currently performing scheduled maintenance. Please check back soon.');
        }

        return $next($request);
    }
}
