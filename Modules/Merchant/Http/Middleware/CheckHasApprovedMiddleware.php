<?php

namespace Modules\Merchant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Merchant\Classes\Facades\MerchantCan;

class CheckHasApprovedMiddleware
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
        if (!MerchantCan::do('isApproved')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Merchant chưa được phê duyệt!'));
        }
        return $next($request);
    }
}
