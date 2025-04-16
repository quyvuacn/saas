<?php

namespace Modules\Merchant\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Merchant;
use App\Models\MerchantInfo;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/register-success';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:merchant');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'account'           => ['required', 'min:5', 'max:255', 'unique:merchant'],
            'merchant_name'     => ['required', 'string', 'min:5', 'max:255', 'unique:merchant_info'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:merchant'],
            'password'          => ['required', 'string', 'min:8', 'max:255'],
            'phone'             => ['required', 'digits:10', 'min:10', 'max:10', 'unique:merchant'],
            'address'           => ['max:255'],
            'company_name'      => ['required', 'min:5', 'max:255']
        ], [
            'account.required'          => 'Tài khoản đăng nhập là bắt buộc',
            'account.min'               => 'Tài khoản đăng nhập có độ dài ký tự tối đa = 255',
            'account.max'               => 'Tài khoản đăng nhập có độ dài ký tự tối thiểu = 5',
            'account.unique'            => 'Tài khoản đăng nhập đã tồn tại',
            'merchant_name.required'    => 'Tên hiển thị là bắt buộc',
            'merchant_name.string'      => 'Tên hiển thị không đúng định dạng ký tự',
            'merchant_name.min'         => 'Tên hiển thị có độ dài ký tự tối thiểu = 5',
            'merchant_name.max'         => 'Tên hiển thị có độ dài ký tự tối đa = 255',
            'merchant_name.unique'      => 'Tên hiển thị merchant đã tồn tại ',
            'company_name.required'     => 'Tên công ty/cá nhân là bắt buộc',
            'company_name.string'       => 'Tên công ty/cá nhân không đúng định dạng ký tự',
            'company_name.min'          => 'Tên công ty/cá nhân có độ dài ký tự tối thiểu = 5',
            'company_name.max'          => 'Tên công ty/cá nhân có độ dài ký tự tối đa = 255',
            'email.required'            => 'Email là bắt buộc',
            'email.string'              => 'Email không đúng định dạng ký tự',
            'email.max'                 => 'Email có độ dài ký tự tối đa = 255',
            'email.email'               => 'Email không đúng định dạng',
            'email.unique'              => 'Email đã tồn tại',
            'password.required'         => 'Mật khẩu là bắt buộc',
            'password.string'           => 'Mật khẩu không đúng định dạng ký tự',
            'password.min'              => 'Mật khẩu có độ dài ký tự tối thiểu = 8',
            'password.max'              => 'Mật khẩu có độ dài ký tự tối đa = 255',
            'phone.digits'              => 'Số điện thoại phải là dạng số',
            'phone.required'            => 'Số điện thoại là bắt buộc',
            'phone.min'                 => 'Số điện thoại có độ dài tối thiểu 10 chữ số',
            'phone.max'                 => 'Số điện thoại có độ dài tối đa 10 chữ số',
            'phone.unique'              => 'Số điện thoại đã tồn tại',
            'address.max'               => 'Địa chỉ có độ dài ký tự tối đa = 255',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $merchant = Merchant::create([
            'account'  => $data['account'],
            'name'     => $data['merchant_name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);
        if ($merchant) {
            $merchant->merchant_code = $merchant->id;
            $merchant->save();
            $merchant_info = [
                'merchant_id'      => $merchant->id,
                'merchant_name'    => $merchant->name,
                'merchant_company' => $data['company_name'],
                'merchant_address' => $data['address'],
            ];
            $merchantInfo = MerchantInfo::create($merchant_info);

            $dataMail = [
                'view' => 'merchant::email.merchant-register',
                'to' => config('mail.list_mail.merchant_register'),
                'data' => ['requestId' => $merchant->id, 'merchant' => $merchant, 'merchantInfo' => $merchantInfo],
                'subject' => '[1giay.vn] Yêu cầu đăng ký Merchant mới!'
            ];
            sendMailCustom($dataMail);
        }
        return $merchant;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('merchant::auth.register');
    }

    // OVERRIDE
    public function guard()
    {
        return Auth::guard('merchant');
    }

    public function registerSuccess()
    {
        return view('merchant::auth.register-success');
    }
}
