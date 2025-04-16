<?php

namespace Modules\Merchant\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Classes\Facades\AdminCan;
use Modules\Merchant\Repositories\LogStatusMachineRepositoryInterface;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Repositories\Eloquent\UserHistoryPaymentRepository;
use Modules\Merchant\Repositories\MachineRepositoryInterface;
use Modules\Merchant\Repositories\ProductRepositoryInterface;
use Modules\Merchant\Repositories\UserDebtRepositoryInterface;
use Modules\Merchant\Repositories\UserRepositoryInterface;

class MerchantController extends Controller
{
    protected $userRepository;
    protected $productRepository;
    protected $userDebtRepository;
    protected $machineRepository;
    protected $userHistoryPaymentRepository;
    protected $logStatusMachineRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ProductRepositoryInterface $productRepository,
        MachineRepositoryInterface $machineRepository,
        UserHistoryPaymentRepository $userHistoryPaymentRepository,
        LogStatusMachineRepositoryInterface $logStatusMachineRepository,
        UserDebtRepositoryInterface $userDebtRepository
    ) {
        // Multi Auth, merchant guard must first order
        $this->middleware('auth:merchant');
        $this->userRepository               = $userRepository;
        $this->productRepository            = $productRepository;
        $this->machineRepository            = $machineRepository;
        $this->userHistoryPaymentRepository = $userHistoryPaymentRepository;
        $this->logStatusMachineRepository   = $logStatusMachineRepository;
        $this->userDebtRepository           = $userDebtRepository;
    }

    public function dashboard()
    {
        if (MerchantCan::do('dashboard')) {
            $totalNewCustomers    = $this->userRepository->getTotalNewUsers()->count();
            $totalProducts        = $this->productRepository->getTotalProducts()->count();
            $totalTodayRevenue    = $this->userHistoryPaymentRepository->getTotalTodayRevenue();
            $productOnMachines    = $this->machineRepository->getProductOnMachines()->get();
            $productOnMachines    = $this->sortingProductOnMachines($productOnMachines);
            $totalDebtUsers       = $this->userDebtRepository->findUserDebts()->count();

            $latestSellingTransactions = $this->userHistoryPaymentRepository->latestSellingTransactions()->get();
            $listLogStatusMachine = $this->logStatusMachineRepository->list();
            return view('merchant::dashboard', compact('totalNewCustomers', 'totalProducts', 'totalTodayRevenue', 'productOnMachines', 'latestSellingTransactions', 'listLogStatusMachine', 'totalDebtUsers'));
        }
        $route = 'merchant.account.profile';
        $mes   = '';
        if (url()->previous() != route('merchant.login') && url()->previous() != route('merchant.register')) {
            $mes = session('error') ?? __('Tài khoản không có quyền hạn này!');
        }
        return redirect()->route($route)->with('error', $mes);
    }

    public function getChartRevenue()
    {
        $latestWeekRevenue = $this->userHistoryPaymentRepository->getLatestWeekRevenue()->get();
        $weekMap           = [
            0 => 'Chủ Nhật',
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
        ];

        $latestWeek = [
            Carbon::now()->subDay(6)->format('Y-m-d') => 0,
            Carbon::now()->subDay(5)->format('Y-m-d') => 0,
            Carbon::now()->subDay(4)->format('Y-m-d') => 0,
            Carbon::now()->subDay(3)->format('Y-m-d') => 0,
            Carbon::now()->subDay(2)->format('Y-m-d') => 0,
            Carbon::now()->subDay(1)->format('Y-m-d') => 0,
            Carbon::now()->format('Y-m-d')            => 0,
        ];

        $data = [];
        if ($latestWeekRevenue->count()) {
            foreach ($latestWeek as $key => $day) {
                $revenue = 0;
                foreach ($latestWeekRevenue as $revenueDay) {
                    if ($revenueDay->created_at->format('Y-m-d') == $key) {
                        $revenue += $revenueDay->transaction_coin;
                    }
                }
                $latestWeek[$key] = $revenue;
                unset($revenue);
            }
        }
        foreach ($latestWeek as $key => $day) {
            $data[$weekMap[Carbon::create($key)->dayOfWeek] . ' (' . Carbon::create($key)->format('d-m-Y') . ')'] = $day;
        }

        return ['label' => array_keys($data), 'data' => array_values($data)];
    }

    private function sortingProductOnMachines($productOnMachines)
    {
        if ($productOnMachines) {
            foreach ($productOnMachines as $productOnMachine) {
                if ($productOnMachine->productLists->count()) {
                    foreach ($productOnMachine->productLists as $productList) {
                        $productOnMachine->percent = number_format($productList->remain_products / $productList->total_products, 4) * 100;
                    }
                } else {
                    $productOnMachine->percent = 0;
                }
            }
        }
        return $productOnMachines->sortBy('percent')->slice(0, 1000);
    }
}
