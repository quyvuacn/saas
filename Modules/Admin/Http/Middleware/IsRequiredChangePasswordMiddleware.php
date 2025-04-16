<?php

namespace Modules\Admin\Http\Middleware;

use Closure;

class IsRequiredChangePasswordMiddleware
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth(ADMIN)->user()->is_required_change_password == 1 && $request->route()->getName() !== 'admin.account.profile') {
            return redirect()->route('admin.account.profile')->with('error', 'Vui lòng đổi mật khẩu với lần đăng nhập đầu tiên!');
        }

        return $next($request);
    }

}
