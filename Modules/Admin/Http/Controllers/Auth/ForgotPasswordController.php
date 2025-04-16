<?php

namespace Modules\Admin\Http\Controllers\Auth;

use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{

    use SendsPasswordResetEmails;

    // OVERIDE
    public function showLinkRequestForm()
    {
        return view('admin::auth.passwords.email');
    }

    // OVERIDE
    protected function credentials(Request $request)
    {
        $user = Admin::where('email', $request->email)->first();
        if ($user && $user->status == 1) {
            return 'email.valid';
        }
        return 'email.invalid';
    }

    // OVERIDE
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);
        $response = $this->credentials($request);
        $token    = $this->generateToken();
        return $response == 'email.valid' ? $this->sendResetLinkResponse($request, $token) : $this->sendResetLinkFailedResponse($request, $response);
    }

    // OVERIDE
    protected function sendResetLinkResponse(Request $request, $token)
    {
        $url = route('admin.password.reset', ['token' => $token]);
        // Send Mail, no Queue
        $dataMail = [
            'view' => 'admin::email.reset-password',
            'to' => $request->email,
            'data' => ['url' => $url],
            'subject' => '[1giay.vn] reset password request!'
        ];
        $isSuccess = sendMailCustom($dataMail);
        $message = $isSuccess ? __('Một liên kết xác minh mới đã được gửi đến địa chỉ email của bạn!') : __('Có lỗi xảy ra. Vui lòng liên hệ admin!');
        return $request->wantsJson() ? new JsonResponse(['message' => $message], 200) : back()->with('status', $message);
    }

    // OVERIDE
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        $message = __('Email này không tồn tại trong hệ thống, hoặc chưa được active!');
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [$message],
            ]);
        }
        return back()->withInput($request->only('email'))->withErrors(['email' => $message]);
    }

    private function generateToken()
    {
        return Str::uuid();
    }
}
