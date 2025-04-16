<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Classes\Facades\AdminCan;
use Modules\Admin\Repositories\Eloquent\LogStatusMachineRepository;
use Modules\Admin\Repositories\LogStatusMachineRepositoryInterface;
use Modules\Admin\Repositories\MachineRepositoryInterface;
use Modules\Admin\Repositories\MachineRequestBackRepositoryInterface;
use Modules\Admin\Repositories\MerchantRepositoryInterface;
use Modules\Admin\Repositories\MerchantRequestMachineRepositoryInterface;
use Modules\Admin\Repositories\ReportStatusMachineRepositoryInterface;
use Modules\Admin\Repositories\SubscriptionRepositoryInterface;
use Modules\Admin\Repositories\SubscriptionRequestRepositoryInterface;

class AdminController extends Controller
{

    protected $merchantRepostitory;
    protected $machineRepository;
    protected $machineRequestBackRepository;
    protected $subscriptionRequestRepository;
    protected $subscriptionRepository;
    protected $merchantRequestMachineRepository;
    protected $reportStatusMachineRepository;
    protected $logStatusMachineRepository;

    public function __construct(
        MerchantRepositoryInterface $merchantRepository,
        MachineRepositoryInterface $machineRepository,
        MachineRequestBackRepositoryInterface $machineRequestBackRepository,
        SubscriptionRequestRepositoryInterface $subscriptionRequestRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        MerchantRequestMachineRepositoryInterface $merchantRequestMachineRepository,
        ReportStatusMachineRepositoryInterface $reportStatusMachineRepository,
        LogStatusMachineRepositoryInterface $logStatusMachineRepository
    )
    {
        $this->middleware('auth:admin');
        $this->middleware('change.password.account');

        $this->merchantRepostitory = $merchantRepository;
        $this->machineRepository = $machineRepository;
        $this->machineRequestBackRepository = $machineRequestBackRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionRequestRepository = $subscriptionRequestRepository;
        $this->merchantRequestMachineRepository = $merchantRequestMachineRepository;
        $this->reportStatusMachineRepository = $reportStatusMachineRepository;
        $this->logStatusMachineRepository = $logStatusMachineRepository;
    }

    public function dashboard()
    {
        if (!AdminCan::do('adm.dashboard')) {
            $route = 'admin.account.profile';
            $mes   = '';
            if (url()->previous() != route('admin.login')) {
                $mes = __('Tài khoản không có quyền hạn này!');
            }
            return redirect()->route($route)->with('error', $mes);
        }

        $totalMerchantRequest = $this->merchantRepostitory->getTotalMerchantRequest();
        $totalMerchantRequestMachine = $this->merchantRequestMachineRepository->getTotalRequet();
        $totalSubscriptionRequest = $this->subscriptionRequestRepository->getTotalRequest();
        $totalMachineRequestBack = $this->machineRequestBackRepository->getTotalNewRequest();
        $listSubscriptionAboutDateToExpire = $this->subscriptionRepository->getSubscriptionAboutToExpire();
        $listSubscriptionExpire = $this->subscriptionRepository->getSubscriptionExpireAndMachineNotBack();
        $listLogStatusMachine = $this->logStatusMachineRepository->list();

        $listProcessing = $this->machineRequestBackRepository->listProcessing();

        return view('admin::dashboard', compact(
            'totalMerchantRequest',
            'totalMerchantRequestMachine',
            'totalSubscriptionRequest',
            'totalMachineRequestBack',
            'listProcessing',
            'listSubscriptionAboutDateToExpire',
            'listLogStatusMachine',
            'listSubscriptionExpire'
        ));
    }

    public function getDataChartMachine()
    {
        $totalStatusMachine = $this->reportStatusMachineRepository->getDataChartActive();
        $label = $data = [];
        foreach ($totalStatusMachine as $statusMachine){
            $time = explode('_', $statusMachine['week_year']);
            $label[] = 'Week ' . $time[0];
            $data[] = $statusMachine['data'];
        }
        return ['label' => $label, 'data' => $data];
    }
}
