<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHotelFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $featureSlug): Response
    {
        // Bypass check if a Titanium Master Admin is logged in (Shadow Login)
        if (auth('titanium')->check()) {
            return $next($request);
        }

        // Check if the auth user belongs to a hotel with specific feature
        $user = auth('admin')->user();

        if ($user && $user->hotel) {
            if (!$user->hotel->hasFeature($featureSlug)) {
                abort(403, 'This feature is not included in your current plan.');
            }
        }

        return $next($request);
    }
}
