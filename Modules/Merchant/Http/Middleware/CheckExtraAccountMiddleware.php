<?php

namespace Modules\Merchant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckExtraAccountMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next, $guard = null)
    {
        $children_count = auth($guard)->user()->children()->count();
        if ($guard == "merchant" && auth($guard)->check() && $children_count < config('merchant.max_extra_account')) {
            return $next($request);
        }
        return redirect()->route('merchant.account.list')->with('error', 'Không thể tạo quá ' . config('merchant.max_extra_account') . ' tài khoản phụ');
    }
}
