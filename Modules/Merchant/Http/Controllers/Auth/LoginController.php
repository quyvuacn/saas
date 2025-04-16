<?php

namespace Modules\Merchant\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Merchant;
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
        $this->middleware('guest:merchant')->except('logout');
    }

    // OVERRIDE
    public function showLoginForm()
    {
        return view('merchant::auth.login');
    }

    // OVERRIDE
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|email',
            'password'        => 'required|min:8',
        ], [
            $this->username() . '.required' => ucfirst($this->username()) . ' là bắt buộc',
            $this->username() . '.email'    => ucfirst($this->username()) . ' không đúng định dạng',
            'password.required'             => 'Password là bắt buộc',
            'password.min'                  => 'Password phải có độ dài ký tự >= 8',
        ]);
    }

    // OVERRIDE
    protected function credentials(Request $request)
    {
        $user = Merchant::where('email', $request->email)->first();

        if ($user) {
            if ($user->status == 0 || $user->status == 1 || $user->status == 4 || $user->status == 2) {
                return ['email' => 'inactive', 'password' => 'Tài khoản chưa được active, hãy liên hệ với Quản trị viên'];
            } else {
                return ['email' => $request->email, 'password' => $request->password, 'status' => 3];
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

        return $request->wantsJson() ? new Response('', 204) : redirect()->route('merchant.login');
    }

    // OVERRIDE
    public function guard()
    {
        return Auth::guard(MERCHANT);
    }
}
