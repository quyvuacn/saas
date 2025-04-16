<?php

namespace App\Http\Middleware;


use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $urlParts = parse_url($request->root());
        $domain = $urlParts['host'] ?? '';
        
        if ($guard == "admin" && Auth::guard($guard)->check()) {
            return redirect('/');
        }
        if ($guard == "merchant" && Auth::guard($guard)->check() && $domain !== env('DOMAIN_REGISTER')) {
            return redirect('/');
        }
        if ($guard == null && Auth::guard($guard)->check()) {
            return redirect('/');
        }
        return $next($request);
    }
}
