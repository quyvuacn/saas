<?php

namespace Modules\Merchant\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Merchant;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/account/profile';

    // OVERIDE
    public function showResetForm(Request $request, $token = null)
    {
        return view('merchant::auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
    }

    // OVERIDE
    protected function credentials(Request $request)
    {
        $user = Merchant::where('email', $request->email)->first();
        if ($user && $user->status == 3) {
            return [
                'email' => 'valid',
                'user'  => $user,
            ];
        }
        return ['email' => 'invalid'];
    }

    // OVERIDE
    public function update(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $response = $this->credentials($request);

        if ($response['email'] === 'valid') {
            $this->resetPassword($response['user'], $request->password);
        }

        return $response['email'] === 'valid' ? $this->sendResetResponse($request, $response) : $this->sendResetFailedResponse($request, $response);
    }

    // OVERIDE
    protected function sendResetResponse(Request $request, $response)
    {
        $message = __('Hệ thống đã thiết lập lại mật khẩu. Mời bạn đăng nhập lại!');

        if ($request->wantsJson()) {
            return new JsonResponse(['message' => $message], 200);
        }

        return redirect($this->redirectPath())->with('status', $message);
    }

    // OVERIDE
    protected function sendResetFailedResponse(Request $request, $response)
    {
        $message = __('Email này không tồn tại trong hệ thống, hoặc chưa được active!');

        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [$message],
            ]);
        }

        return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => $message]);
    }

    protected function rules()
    {
        return [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }

    protected function validationErrorMessages()
    {
        return [
            'token.required'     => 'Token là bắt buộc',
            'email.required'     => 'Email là bắt buộc',
            'email.email'        => 'Email không đúng định dạng',
            'password.required'  => 'Mật khẩu là bắt buộc',
            'password.confirmed' => 'Mật khẩu chưa được xác nhân',
            'password.min'       => 'Mật khẩu yêu cầu độ dài tối thiểu = 8 ký tự',
        ];
    }

    // OVERIDE
    protected function guard()
    {
        return Auth::guard(MERCHANT);
    }
}
