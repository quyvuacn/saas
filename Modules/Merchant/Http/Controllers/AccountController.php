<?php

namespace Modules\Merchant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Admin\Classes\AdminCan;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Http\Requests\AccountProfileRequest;
use Modules\Merchant\Http\Requests\AccountRequest;
use Modules\Merchant\Http\Requests\AccountSettingRequest;
use Modules\Merchant\Http\Requests\AccountUpdateRequest;
use Modules\Merchant\Repositories\AccountRepositoryInterface;
use Modules\Merchant\Repositories\LogActionMerchantRepositoryInterface;
use Modules\Merchant\Repositories\RoleRepositoryInterface;

class AccountController extends Controller
{
    protected $accountRepository;

    protected $roleRepository;

    protected $user;

    protected $logActionMerchantRepository;

    public function __construct(
        AccountRepositoryInterface $accountRepository,
        RoleRepositoryInterface $roleRepository,
        LogActionMerchantRepositoryInterface $logActionMerchantRepository
    )
    {
        $this->middleware('auth:merchant');
        $this->accountRepository = $accountRepository;
        $this->roleRepository = $roleRepository;
        $this->logActionMerchantRepository = $logActionMerchantRepository;
    }

    public function list(Request $request)
    {
        if (!MerchantCan::do('account.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->accountRepository->list();
        }
        return view('merchant::account.list');
    }

    public function permission(Request $request)
    {
        if (!MerchantCan::do('permission.list')) {
            return redirect()->route('merchant.account.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->accountRepository->permission($request);
        }
        return view('merchant::account.permission');
    }

    public function permissionChange($account, Request $request)
    {
        if (!MerchantCan::do('permission.edit')) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn này!')];
        } else {
            $account = $this->accountRepository->findAccountByID($account);
            if (!$account) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Quyền không tồn tại!')];
            } elseif (!MerchantCan::do('permission.change', $account)) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn với Quyền này!')];
            } else {
                $response = $this->accountRepository->permissionChange($account, $request);

                $attribute['content_request'] = [
                    'User ID'   => $account->id,
                    'User Name' => $account->name,
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);

                return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
            }
        }
    }

    public function create()
    {
        if (!MerchantCan::do('account.edit')) {
            return redirect()->route('merchant.account.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $roles = $this->roleRepository->roleWithPermissions();
        return view('merchant::account.create', compact('roles'));
    }

    public function edit($account)
    {
        $account = $this->accountRepository->findAccountByID($account);
        if (!$account) {
            return redirect()->route('merchant.account.list')->with('error', __('Tài khoản không tồn tại'));
        }
        if (!MerchantCan::do('account.change', $account)) {
            return redirect()->route('merchant.account.list')->with('error', __('Tài khoản không có quyền hạn với account này!'));
        }
        $roles = $this->roleRepository->roleWithPermissions();
        return view('merchant::account.create', compact('roles','account'));
    }

    public function destroy($merchant, Request $request)
    {
        $merchant = $this->accountRepository->findAccountByID($merchant);
        if (!$merchant || !MerchantCan::do('account.change', $merchant)) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn')];
        } else {
            $response = $this->accountRepository->destroy($merchant);

            $attribute['content_request'] = [
                'User ID' => $merchant->id,
                'User Name' => $merchant->name
            ];
            $this->logActionMerchantRepository->createAction($request, $attribute);

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    public function update($account, AccountUpdateRequest $request)
    {
        $account = $this->accountRepository->findAccountByID($account);
        if (!$account) {
            return redirect()->route('merchant.account.list')->with('error', __('Tài khoản không tồn tại'));
        }
        if (!MerchantCan::do('account.change', $account)) {
            return redirect()->route('merchant.account.list')->with('error', __('Tài khoản không có quyền hạn với account này!'));
        }
        $response = $this->accountRepository->updateAccount($account, $request);

        $attribute['content_request'] = [
            'User ID' => $account->id,
            'User Name' => $account->name
        ];
        $this->logActionMerchantRepository->createAction($request, $attribute);
        // TODO: send password
        return redirect()->route('merchant.account.list')->with($response['alert'], $response['message']);
    }

    public function store(AccountRequest $request)
    {
        if (!MerchantCan::do('account.edit')) {
            return redirect()->route('merchant.account.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }

        $response = $this->accountRepository->storeAccount($request);

        [$account, $password] = $response['data'];

        // Auto make Pass and SendMail
        if($request->pass_make == 0 && $response['status']){
            $dataMail = [
                'view' => 'merchant::email.merchant-register-random-password',
                'to' => $account->email,
                'data' => ['account' => $account, 'password' => $password],
                'subject' => '[1giay.vn] Yêu cầu đăng ký Merchant mới!'
            ];
            sendMailCustom($dataMail);
        }

        $attribute['content_request'] = [
            'User ID' => $account->id,
            'User Name' => $account->name
        ];
        $this->logActionMerchantRepository->createAction($request, $attribute);
        // TODO: send password
        return redirect()->route('merchant.account.list')->with($response['alert'], $response['message']);
    }

    public function profile()
    {
        return view('merchant::account.profile');
    }

    public function updateProfile(AccountProfileRequest $request)    {
        $merchant = $this->accountRepository->findAccountByID(auth(MERCHANT)->id());
        if (!$merchant) {
            abort('404');
        }
        $response = $this->accountRepository->updateProfile($merchant, $request);

        $this->logActionMerchantRepository->createAction($request, ['content_request' => []]);

        return redirect()->route('merchant.account.profile')->with($response['alert'], $response['message']);
    }

    public function setting()
    {
        if (!MerchantCan::do('setting.edit')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $account = auth(MERCHANT)->user();
        if ($account->parent_id == 0) {
            $merchant = $account;
        } else {
            $merchant = $account->commonMerchant();
        }
        $merchantInfo      = $merchant->merchantInfo;
        $bankInfo          = $merchantInfo->merchant_bank_info ? array_reverse(json_decode($merchantInfo->merchant_bank_info)) : [];
        $bankAccountNumber = count($bankInfo);
        return view('merchant::account.setting', compact('merchant', 'merchantInfo', 'bankInfo', 'bankAccountNumber'));
    }

    public function updateSetting(AccountSettingRequest $request)    {
        if (!MerchantCan::do('setting.edit')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $account = auth(MERCHANT)->user();
        if ($account->parent_id == 0) {
            $merchant = $account;
        } else {
            $merchant = $account->commonMerchant();
        }
        if (!$merchant) {
            return redirect()->route('merchant.dashboard')->with('error', __('Merchant không tồn tại!'));
        }

        $this->logActionMerchantRepository->createAction($request, ['content_request' => []]);

        $response = $this->accountRepository->updateSetting($account, $merchant, $request);
        return redirect()->route('merchant.account.setting')->with($response['alert'], $response['message']);
    }

    public function selfHistory(Request $request)
    {
        if ($request->ajax()) {
            return $this->logActionMerchantRepository->list($request);
        }

        return view('merchant::account.history', compact('request', 'merchant'));
    }

    public function history(Request $request)
    {
        $merchant = auth(MERCHANT)->user();
        // View other History
        if ($request->route()->getName() === 'merchant.account.historyMerchant') {
            if($request->id == auth(MERCHANT)->id()){
                return redirect()->route('merchant.account.history');
            } elseif ($merchant->getMerchantID() !== $merchant->id) {
                return redirect()->route('merchant.account.history')->with('error', __('Tài khoản không có quyền xem lịch sử người dùng khác!'));
            }
        }

        $merchant = $request->id ? $this->accountRepository->findAccountByID($request->id) : auth(MERCHANT)->user();
        if(empty($merchant)){
            return redirect()->route('merchant.account.history')->with('error', __('Tài khoản không tồn tại!'));
        }

        if(!empty($request->id)){
            if($request->id == auth(MERCHANT)->id()){
                return redirect()->route('merchant.account.history');
            }
            $listAccountId = $this->accountRepository->findChildrenAccounts()->pluck('id');
            if(!$listAccountId->contains($request->id)){
                return redirect()->route('merchant.account.history')->with('error', __('Tài khoản không thuộc Merchant!'));
            }
        }

        if ($request->ajax()) {
            return $this->logActionMerchantRepository->list($request);
        }

        return view('merchant::account.history', compact('request', 'merchant'));
    }
}
