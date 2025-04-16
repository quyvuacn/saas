<?php

namespace Modules\Merchant\Http\Controllers;

use App\Exports\UserExport;
use App\Exports\UserRechargeExport;
use App\Imports\UserImport;
use App\Models\UserCoinRequest;
use App\Models\UserHistoryPayment;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Admin\Http\Requests\UserSearchRequest;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Http\Requests\UserCoinApproveOptionRequest;
use Modules\Merchant\Http\Requests\UserCreditApproveRequest;
use Modules\Merchant\Http\Requests\UserRechargeCreateRequest;
use Modules\Merchant\Http\Requests\UserRechargeSearchRequest;
use Modules\Merchant\Http\Requests\UserRequest;
use Modules\Merchant\Jobs\UserReceivedDebtEmailJob;
use Modules\Merchant\Repositories\LogActionMerchantRepositoryInterface;
use Modules\Merchant\Repositories\UserCoinRequestRepositoryInterface;
use Modules\Merchant\Repositories\UserDebtRepositoryInterface;
use Modules\Merchant\Repositories\UserHistoryPaymentRepositoryInterface;
use Modules\Merchant\Repositories\UserRepositoryInterface;

use App\Exports\UserDebtExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    protected $userRepository;
    protected $userCoinRequestRepository;
    protected $userHistoryPaymentRepository;
    protected $userDebtRepository;
    protected $logActionMerchantRepository;

    public function __construct(UserRepositoryInterface $userRepository,
        UserCoinRequestRepositoryInterface $userCoinRequestRepository,
        UserHistoryPaymentRepositoryInterface $userHistoryPaymentRepository,
        LogActionMerchantRepositoryInterface $logActionMerchantRepository,
        UserDebtRepositoryInterface $userDebtRepository
    )
    {
        $this->middleware('auth:merchant');
        $this->userRepository = $userRepository;
        $this->userCoinRequestRepository = $userCoinRequestRepository;
        $this->userHistoryPaymentRepository = $userHistoryPaymentRepository;
        $this->userDebtRepository = $userDebtRepository;
        $this->logActionMerchantRepository = $logActionMerchantRepository;
    }

    // List User
    public function list(Request $request)
    {
        if (!MerchantCan::do('user.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->userRepository->list($request);
        }
        return view('merchant::user.list');
    }

    // Create User
    public function create()
    {
        if (!MerchantCan::do('user.edit')) {
            return redirect()->route('merchant.user.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        return view('merchant::user.create');
    }

    // Save User
    public function store(UserRequest $request)
    {
        if (!MerchantCan::do('user.edit')) {
            return redirect()->route('merchant.user.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $response = $this->userRepository->store($request);
        // TODO: send password
        if($response['status']){
            $password = $response['data']['password'];
            $account = $response['data']['account'];

            $dataMail = [
                'view' => 'merchant::email.user-register',
                'to' => $account->email,
                'data' => ['password' => $password, 'account' => $account],
                'subject' => '[1giay.vn] Thông báo đăng ký tài khoản thành công!'
            ];
            sendMailCustom($dataMail);

            $attribute['content_request'] = [
                'User ID' => $account->id
            ];
            $this->logActionMerchantRepository->createAction($request, $attribute);
        }

        return redirect()->route('merchant.user.list')->with($response['alert'], $response['message']);
    }

    // Import Excel User
    public function import(Request $request)
    {
        if (!MerchantCan::do('user.edit')) {
            return redirect()->route('merchant.user.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $listUser = $this->userRepository->findAll()->get()->pluck('email');
        $file     = request()->file('file');
        $import   = new UserImport($listUser);
        $import->import($file);
        $this->logActionMerchantRepository->createAction($request, ['content_request' => []]);
        return back()->with('message', 'Import Nhân viên thành công!');
    }

    //  Export Excel User
    public function exportUser(Request $request)
    {
        $userLists = $this->userRepository->findAll()->limit(1)->get();
        $this->logActionMerchantRepository->createAction($request, ['content_request' => []]);
        return Excel::download(new UserExport($userLists), 'UserInfo.xlsx');
    }

    //  Export Excel User
    public function rechargeExport(Request $request)
    {
        if (!MerchantCan::do('user.coin.request.edit')) {
            return redirect()->route('merchant.user.recharge')->with('error', __('Tài khoản không có quyền hạn này!'));
        }

        $userDebts = $this->userCoinRequestRepository->findAllCoinRequests();
        $this->logActionMerchantRepository->createAction($request, ['content_request' => []]);
        return Excel::download(new UserRechargeExport($userDebts), 'UserRechargeExport.xlsx');
    }

    // Delete User
    public function destroy($user, Request $request){
        $user = $this->userRepository->findUserByID($user);
        if ($user && MerchantCan::do('user.change', $user)) {
            $response = $this->userRepository->destroy($user);

            if($response['status']) {
                $attribute['content_request'] = [
                    'User ID' => $user->id,
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);
            }

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        } else {
            return ['status' => false, 'alert' => 'error', 'message' => __('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn')];
        }
    }

    // Delete User Credit
    public function destroyCredit($user, Request $request){
        $user = $this->userRepository->findUserByID($user);
        if ($user && MerchantCan::do('user.credit.change', $user)) {
            $response = $this->userRepository->destroyCredit($user);
            if($response['status']){

                $attribute['content_request'] = [
                    'User ID' => $user->id,
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);

                $dataMail = [
                    'view' => 'merchant::email.merchant-approve-credit',
                    'to' => $user->email,
                    'data' => ['account' => $user],
                    'subject' => '[1giay.vn] Xoá tín dụng người dùng!'
                ];
                sendMailCustom($dataMail);
            }
            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        } else {
            return ['status' => false, 'alert' => 'error', 'message' => __('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn')];
        }
    }

    public function destroyCoinRequest($coin, Request $request){
        $coin = $this->userCoinRequestRepository->findCoinRequestByID($coin);
        if (!$coin || !$coin->user()->first() || !MerchantCan::do('user.coin.request.change', $coin->user()->first())) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn')];
        } else {
            $response = $this->userCoinRequestRepository->destroyCoinRequest($coin);

            if($response['status']) {
                $attribute['content_request'] = [
                    'ID' => $coin->id,
                    'User ID' => $coin->user_id,
                    'Coin' => $coin->coin
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);
            }

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    // List User credit
    public function credit(Request $request)
    {
        if (!MerchantCan::do('user.credit.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->userRepository->credit($request);
        }
        return view('merchant::user.credit');
    }

    // User Debt List
    public function debt(Request $request)
    {
        if (!MerchantCan::do('user.debt.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->userDebtRepository->list($request);
        }
        $isDebtLocked = $this->userDebtRepository->isUserDebtLocked();
        return view('merchant::user.credit-dept', compact('isDebtLocked'));
    }

    // Received Debt
    public function debtReceived($debtUser, Request $request)
    {
        $debtUser = $this->userDebtRepository->findDebtUserByID($debtUser);
        if (!$debtUser || !$debtUser->user()->first() || !MerchantCan::do('user.debt.change', $debtUser->user()->first())) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn')];
        } else {

            $response = $this->userDebtRepository->receivedDebt($debtUser);

            if($response['status']){
                $attribute['content_request'] = [
                    'User ID'    => $debtUser->user_id,
                    'User Email' => $debtUser->user_id,
                    'Debt'       => $debtUser->debt,
                ];

                $this->logActionMerchantRepository->createAction($request, $attribute);

                dispatch(new UserReceivedDebtEmailJob($debtUser->user))->delay(Carbon::now()->addSeconds(15));
            }

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    // Active Debt mode
    public function debtCollectionActivation(Request $request)
    {
        if (!MerchantCan::do('user.debt.edit')) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn')];
        } else {

            $response = $this->userDebtRepository->debtCollectionActivation();

            if($response['status']){
                $attribute['content_request'] = [
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);
            }

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    // Disable Debt mode
    public function debtCollectionDisable(Request $request)
    {
        if (!MerchantCan::do('user.debt.edit')) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn')];
        } else {

            $response = $this->userDebtRepository->debtCollectionDisable();

            if($response['status']){
                $attribute['content_request'] = [
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);
            }

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    // Export Debt list
    public function export(Request $request)
    {
        if (!MerchantCan::do('user.debt.edit')) {
            return redirect()->route('merchant.user.debt')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $isDebtLocked = $this->userDebtRepository->isUserDebtLocked();

        if (!$isDebtLocked) {
            return redirect()->back()->with('error', 'Không có công nợ nào!');
        }

        $userDebts = $this->userDebtRepository->findUserDebts();

        $this->logActionMerchantRepository->createAction($request, ['content_request' => []]);

        return Excel::download(new UserDebtExport($userDebts), 'UserDebts.xlsx');
    }

    public function rechargeCreate()
    {
        if (!MerchantCan::do('user.coin.request.edit')) {
            return redirect()->route('merchant.user.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $users = $this->userRepository->getAllUserOfMerchant()->pluck('email');
        return view('merchant::user.recharge-create', compact('users'));
    }

    public function rechargeStore(UserRechargeCreateRequest $request)
    {
        $user    = $this->userRepository->findUserByID($request->user_id);
        $message = '';
        if (!$user) {
            $message = __('Tài khoản không tồn tại');
        } elseif (!MerchantCan::do('user.coin.request.change', $user)) {
            $message = __('Tài khoản không có quyền hạn với User này');
        }

        if (!$user || !MerchantCan::do('user.coin.request.change', $user)) {
            return ['status' => false, 'alert' => 'error', 'message' => $message];
        } else {
            $response = $this->userRepository->rechargeStore($request);

            if($response['status']) {
                $data = $response['data'];
                $attribute['content_request'] = [
                    'ID' => $data->id,
                    'User ID' => $data->user_id,
                    'Coin' => $data->coin,
                    'Message' => $data->message
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);
            }

            return ['status' => $response['status'], 'request_id' => $response['data']['id'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    public function recharge(Request $request)
    {
        if (!MerchantCan::do('user.coin.request.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->userRepository->recharge($request);
        }
        return view('merchant::user.recharge');
    }

    // Optional User Search
    public function rechargeSearch(UserRechargeSearchRequest $request)
    {
        if (!MerchantCan::do('user.coin.request.edit')) {
            return redirect()->route('merchant.user.recharge')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $approve = $this->userRepository->rechargeSearch($request);
        return view('merchant::user.approve-option',['approve'=>$approve]);
    }

    // Seasrch User to create Request
    public function userSearch(UserSearchRequest $request)
    {
        $user = $this->userRepository->userSearch($request->email);
        if (!$user || !MerchantCan::do('user.coin.request.edit')) {
            return ['status' => false, 'alert' => 'error', 'message' => __('User không tồn tại, hoặc Tài khoản không có quyền hạn!')];
        } else {
            return ['status' => true, 'data' => $this->userRepository->userSearch($request->email), 'alert' => 'message', 'message' => __('Tìm được Tài khoản người dùng')];
        }
    }

    // Credit Approve
    public function approve($approve)
    {
        $approve = $this->userRepository->findUserByID($approve);
        if (!$approve || !MerchantCan::do('user.credit.change', $approve)) {
            return redirect()->route('merchant.user.list')->with('message', __('User không tồn tại, hoặc Tài khoản không có quyền hạn!'));
        }
        $merchant = auth(MERCHANT)->user();
        $approve = $this->userRepository->findUserToApprove($approve->id, $merchant->getMerchantID());
        if (!$approve) {
            abort('404');
        }
        return view('merchant::user.approve', compact('approve'));
    }

    // Credit Approve Post method
    public function approveCredit($approve, UserCreditApproveRequest $userRequest)
    {
        $approve = $this->userRepository->findUserByID($approve);
        if (!$approve || !MerchantCan::do('user.credit.change', $approve)) {
            return redirect()->route('merchant.user.credit')->with('error', __('User không tồn tại, hoặc Tài khoản không có quyền hạn!'));
        }

        $response = $this->userRepository->approveCredit($approve, $userRequest);
        if($response['status']){

            $attribute['content_request'] = [
                'User ID' => $approve->user_id,
                'Credit Quota' => $approve->credit_quota
            ];
            $this->logActionMerchantRepository->createAction($userRequest, $attribute);

            $dataMail = [
                'view' => 'merchant::email.merchant-approve-credit',
                'to' => $approve->email,
                'data' => ['account' => $approve],
                'subject' => '[1giay.vn] Duyệt tín dụng!'
            ];
            sendMailCustom($dataMail);
        }
        return redirect()->route('merchant.user.credit')->with($response['alert'], $response['message']);
    }

    // Optional Coin Approve
    public function approveOption($approve)
    {
        $approve = $this->userCoinRequestRepository->findCoinRequestByID($approve);
        if (!$approve || !$approve->user()->first() || !MerchantCan::do('user.coin.request.change', $approve->user()->first())) {
            return redirect()->route('merchant.user.list')->with('error', __('Yêu cầu không tồn tại, hoặc Tài khoản không có quyền hạn này!'));
        }
        return view('merchant::user.approve-option', ['approve' => $approve]);
    }

    // Optional Coin Approve save()
    public function approveOptionStore(UserCoinRequest $approve, UserCoinApproveOptionRequest $request)
    {
        if (!MerchantCan::do('user.coin.request.change', $approve->user()->first())) {
            return redirect()->route('merchant.user.list')->with('error', __('Yêu cầu không tồn tại, hoặc Tài khoản không có quyền hạn này!'));
        }
        $response = $this->userCoinRequestRepository->approveOptionStore($approve, $request);

        if($response['status']){
            $userDebt = $this->userDebtRepository->findDebByUserID($approve->user->id);
            if ($userDebt) {
                $this->userDebtRepository->decreaseUserDebt($userDebt, $request->user_coin);
            }
            $attribute['content_request'] = [
                'User ID' => $approve->user_id,
                'Coin' => $request->user_coin
            ];
            $this->logActionMerchantRepository->createAction($request, $attribute);
        }

        return redirect()->route('merchant.user.recharge')->with($response['alert'], $response['message']);
    }

    public function quickApprove($coin, Request $request)
    {
        $coin = $this->userCoinRequestRepository->findCoinRequestByID($coin);
        if (!$coin || !$coin->user()->first() || !MerchantCan::do('user.coin.request.change', $coin->user()->first())) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn')];
        } else {
            $response = $this->userCoinRequestRepository->quickApprove($coin);
            if ($coin->user) {
                $this->userHistoryPaymentRepository->createHistoryPayment($coin, $request);
                $userDebt = $this->userDebtRepository->findDebByUserID($coin->user->id);
                if ($userDebt) {
                    $this->userDebtRepository->decreaseUserDebt($userDebt, $coin->coin);
                }
            }
            $attribute['content_request'] = [
                'User ID' => $coin->user_id,
                'Coin' => $coin->user->coin
            ];
            $this->logActionMerchantRepository->createAction($request, $attribute);

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    public function approveAllRequest(Request $request)
    {
        return false;
    }
}
