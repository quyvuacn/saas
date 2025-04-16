<?php

namespace Modules\Admin\Http\Controllers\Auth;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    // OVERRIDE
    public function showLoginForm()
    {
        return view('admin::auth.login');
    }

    // OVERRIDE
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|email',
            'password' => 'required|min:8',
        ]);
    }

    // OVERRIDE
    protected function credentials(Request $request)
    {
        $user = Admin::where('email', $request->email)->first();

        if ($user) {
            if ($user->status == 0) {
                return ['email' => 'inactive', 'password' => 'Your account is not active, please contact Admin'];
            } else {
                return ['email' => $request->email, 'password' => $request->password, 'status' => 1];
            }
        }
        return $request->only($this->username(), 'password');
    }

    // OVERRIDE
    protected function sendFailedLoginResponse(Request $request)
    {
        $fields = $this->credentials($request);

        if ($fields['email'] == 'inactive') {
            $errors['email'] = $fields['password'];

        } else {
            $errors = [$this->username() => trans('auth.failed')];
        }

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()->withInput($request->only($this->username(), 'remember'))->withErrors($errors);
    }

    // OVERRIDE
    public function logout(Request $request)
    {
        $this->guard()->logout();

        // $request->session()->invalidate(); // Multi logout
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson() ? new Response('', 204) : redirect()->route('admin.login');
    }

    // OVERRIDE
    public function guard()
    {
        return Auth::guard(ADMIN);
    }

    protected function attemptLogin(Request $request)
    {
        if($this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        )) {
            $account = Admin::where('email', $request->email)->first();
            $account->last_login = date('Y-m-d H:i:s');
            $account->save();
            return true;
//            if($account->is_required_change_password == 1){
//                return redirect()->route('admin.account.profile');
//            }
        }
        return false;
    }
}
