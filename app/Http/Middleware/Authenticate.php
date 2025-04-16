<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('merchant.login');
        }
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $response = $this->authenticate($request, $guards);

        if (!$response) {
            switch ($guards[0]) {
                case 'api':
                    $response['message'] = 'Not Auth';
                    $response['code']    = 401;
                    $response['data']    = null;
                    return response()->json($response, 401);
                    break;
                case 'admin':
                    return redirect()->route('admin.login');
                    break;
                case 'merchant':
                    return redirect()->route('merchant.login');
                    break;
                // case null:
                //     return redirect()->route('login');
                //     break;
            }
        }

        return $next($request);
    }

    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        $auth = false;
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                $auth = true;
            }
        }
        return $auth;
    }
}
