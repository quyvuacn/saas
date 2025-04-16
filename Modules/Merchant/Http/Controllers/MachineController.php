<?php

namespace Modules\Merchant\Http\Controllers;

use App\Exports\SellingHistoryExport;
use App\Exports\UserRechargeExport;
use App\Models\Machine;
use App\Models\ProductSaleHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Http\Requests\MachineRequest;
use Modules\Merchant\Repositories\LogActionMerchantRepositoryInterface;
use Modules\Merchant\Repositories\MachineRepositoryInterface;
use Modules\Merchant\Repositories\MerchantRequestMachineRepositoryInterface;
use Yajra\DataTables\DataTables;

class MachineController extends Controller
{
    protected $machineRepository;

    protected $merchantRequestMachineRepository;

    protected $logActionMerchantRepository;

    public function __construct(MachineRepositoryInterface $machineRepository,
        MerchantRequestMachineRepositoryInterface $merchantRequestMachineRepository,
        LogActionMerchantRepositoryInterface $logActionMerchantRepository
    )
    {
        $this->middleware('auth:merchant');
        $this->machineRepository = $machineRepository;
        $this->merchantRequestMachineRepository = $merchantRequestMachineRepository;
        $this->logActionMerchantRepository = $logActionMerchantRepository;
    }

    public function list(Request $request)
    {
        if (!MerchantCan::do('machine.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->machineRepository->list($request);
        }
        return view('merchant::machine.list');
    }

    public function requestHistory(Request $request)
    {
        if (!MerchantCan::do('machine_request.history.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->merchantRequestMachineRepository->requestHistory($request);
        }
        return view('merchant::machine.request-history');
    }

    public function listRequest()
    {
        if (!MerchantCan::do('machine_request.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $merchant = auth(MERCHANT)->user();
        $machineRequests = $this->merchantRequestMachineRepository->merchantMachines($merchant->getMerchantID())->get();
        if (!$machineRequests || $machineRequests->count() < 1) {
            return redirect()->route('merchant.machine.request');
        } else {
            return view('merchant::machine.request-list', compact('machineRequests'));
        }
    }

    // Create new Machien Request
    public function request()
    {
        if (!MerchantCan::do('machine_request.list')) {
            return redirect()->route('merchant.machine.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $merchant = auth(MERCHANT)->user();
        $machineRequests = $this->merchantRequestMachineRepository->merchantMachines($merchant->getMerchantID())->get();
        if ($machineRequests && $machineRequests->count() >= 6) {
            return redirect()->route('merchant.machine.listRequest')->with('error', __('Số yêu cầu vượt quá giới hạn cho phép!'));
        }
        return view('merchant::machine.request-create');
    }

    public function editRequest($request)
    {
        $request = $this->merchantRequestMachineRepository->findNewRequestByID($request);
        if (!$request) {
            return redirect()->route('merchant.machine.listRequest')->with('error', __('Yêu cầu không tồn tại!'));
        }
        if (!MerchantCan::do('machine_request.change', $request)) {
            return redirect()->route('merchant.machine.listRequest')->with('error', __('Tài khoản không có quyền hạn với yêu cầu này!'));
        }
        return view('merchant::machine.request-edit', compact('request'));
    }

    public function updateRequest(MachineRequest $requestChanges, $request)
    {
        $request = $this->merchantRequestMachineRepository->findNewRequestByID($request);
        if (!$request) {
            return redirect()->route('merchant.machine.listRequest')->with('error', __('Yêu cầu không tồn tại!'));
        }
        if (!MerchantCan::do('machine_request.change', $request)) {
            return redirect()->route('merchant.machine.listRequest')->with('error', __('Tài khoản không có quyền hạn với yêu cầu này!'));
        }
        $response = $this->merchantRequestMachineRepository->updateRequest($request, $requestChanges);

        $attribute['content_request'] = [
            'ID' => $request->id,
            'Content' => $request->title
        ];
        $this->logActionMerchantRepository->createAction($requestChanges, $attribute);

        return redirect()->route('merchant.machine.listRequest')->with($response['alert'], $response['message']);
    }

    public function requestMachine(MachineRequest $request)
    {
        if (!MerchantCan::do('machine_request.list')) {
            return redirect()->route('merchant.machine.listRequest')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $response = $this->merchantRequestMachineRepository->requestMachine($request);

        $attribute['content_request'] = [
            'ID' => $response['data'],
            'Content' => $request->title
        ];
        $this->logActionMerchantRepository->createAction($request, $attribute);

        if($response['status'] && !empty(auth(MERCHANT)->user()->email)){
            $dataMail = [
                'view' => 'merchant::email.merchant-request-machine',
                'to' => config('mail.list_mail.merchant_request_machine'),
                'data' => ['requestId' => $response['data'], 'merchant' => auth(MERCHANT)->user(), 'machineCount' => $request->machine_request_count],
                'subject' => '[1giay.vn] Yêu cầu cấp thêm máy bán hàng!'
            ];
            sendMailCustom($dataMail);
        }

        return redirect()->route('merchant.machine.listRequest')->with($response['alert'], $response['message']);
    }

    public function deleteRequest($request, Request $requestAll)
    {
        $request = $this->merchantRequestMachineRepository->findNewRequestByID($request);
        if (!$request) {
            return redirect()->route('merchant.machine.listRequest')->with('error', __('Yêu cầu không tồn tại!'));
        }
        if (!MerchantCan::do('machine_request.change', $request)) {
            return redirect()->route('merchant.machine.listRequest')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $response = $this->merchantRequestMachineRepository->deleteRequest($request);

        $attribute['content_request'] = [
            'ID' => $request->id,
            'Content' => $request->title
        ];
        $this->logActionMerchantRepository->createAction($requestAll, $attribute);

        return redirect()->route('merchant.machine.listRequest')->with($response['alert'], $response['message']);
    }

    public function changeAddress($machine, Request $request)
    {
        $merchant = auth(MERCHANT)->user();
        $machine  = $this->machineRepository->findMachineOfMerchantByID($machine, $merchant->getMerchantID());
        if (!$machine || !MerchantCan::do('machine.change', $machine)) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn')];
        } else {
            $response = $this->machineRepository->changeAddress($machine, $request);

            $attribute['content_request'] = [
                'ID' => $machine->id,
                'Machine' => $machine->name,
                'Address' => $request->address
            ];
            $this->logActionMerchantRepository->createAction($request, $attribute);

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    public function requestBack($machine, Request $request)
    {
        $merchant = auth(MERCHANT)->user();
        $machine = $this->machineRepository->findMachineOfMerchantByID($machine, $merchant->getMerchantID());
        if (!$machine) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Máy bán hàng không tồn tại!')];
        } else {
            if (!MerchantCan::do('machine.change', $machine)) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn!')];
            } elseif ($machine->newRequestBack) {
                return ['status' => false, 'alert' => 'error', 'message' => 'Thuê bao đang chờ xử lý!'];
            } else {
                $response = $this->machineRepository->requestBack($machine, $request);

                if($response['status']){
                    $attribute['content_request'] = [
                        'ID' => $response['data'],
                        'Machine ID' => $machine->id,
                        'Machine' => $machine->name,
                        'Content' => $request->request_content
                    ];
                    $this->logActionMerchantRepository->createAction($request, $attribute);
                    $dataMail = [
                        'view' => 'merchant::email.machine-request-back',
                        'to' => config('mail.list_mail.machine_request_back'),
                        'data' => ['requestId' => $response['data'], 'merchant' => auth(MERCHANT)->user(), 'requestBack' => $request, 'machine' => $machine],
                        'subject' => '[1giay.vn] Yêu cầu trả máy bán hàng!'
                    ];
                    sendMailCustom($dataMail);
                }
                return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
            }
        }
    }

    public function historiesExport(Request $request)
    {
        if (!MerchantCan::do('selling.history.list')) {
            return redirect()->route('merchant.machine.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }

        $sellingHistories = $this->machineRepository->findAllHistories();
        $this->logActionMerchantRepository->createAction($request, ['content_request' => []]);
        return Excel::download(new SellingHistoryExport($sellingHistories), 'SellingHistoryExport.xlsx');
    }

    public function history(Request $request)
    {
        if (!MerchantCan::do('selling.history.list')) {
            return redirect()->route('merchant.machine.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->machineRepository->history($request);
        }

        return view('merchant::machine.history');
    }
}
