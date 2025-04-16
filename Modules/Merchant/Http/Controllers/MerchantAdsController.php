<?php

namespace Modules\Merchant\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Http\Requests\MerchantAdsRequest;
use Modules\Merchant\Repositories\LogActionMerchantRepositoryInterface;
use Modules\Merchant\Repositories\MachineRepositoryInterface;
use Modules\Merchant\Repositories\MerchantAdsRepositoryInterface;
use Psy\Util\Json;
use Yajra\DataTables\DataTables;

class MerchantAdsController extends Controller
{
    protected $merchantAdsRepository;

    protected $machineRepository;

    protected $logActionMerchantRepository;

    public function __construct(MerchantAdsRepositoryInterface $merchantAdsRepository,
        MachineRepositoryInterface $machineRepository,
        LogActionMerchantRepositoryInterface $logActionMerchantRepository
    )
    {
        $this->middleware('auth:merchant');
        $this->merchantAdsRepository = $merchantAdsRepository;
        $this->machineRepository = $machineRepository;
        $this->logActionMerchantRepository = $logActionMerchantRepository;
    }

    public function list(Request $request)
    {
        if (!MerchantCan::do('ads.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->merchantAdsRepository->list();
        }
        return view('merchant::ads.list');
    }

    public function create()
    {
        if (!MerchantCan::do('ads.edit')) {
            return redirect()->route('merchant.ads.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $machines = $this->machineRepository->listActiveMachines()->get();
        return view('merchant::ads.create', compact('machines'));
    }

    public function edit($ads)
    {
        $ads = $this->merchantAdsRepository->findAdsByID($ads);
        if (!$ads || !MerchantCan::do('ads.change', $ads)) {
            return redirect()->route('merchant.ads.list')->with('error', __('Quảng cáo không tồn tại, hoặc Tài khoản không có quyền hạn!'));
        }
        $ads->image = makeImageAds($ads->image);
        $adsMachines = $ads->machines->pluck('id');
        $machines = $this->machineRepository->listActiveMachines()->get();
        return view('merchant::ads.edit', compact('ads', 'machines', 'adsMachines'));
    }

    public function update(MerchantAdsRequest $request, $ads)
    {
        $ads = $this->merchantAdsRepository->findAdsByID($ads);
        if (!$ads || !MerchantCan::do('ads.change', $ads)) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Quảng cáo không tồn tại, hoặc Tài khoản không có quyền hạn!')];
        } else {
            $response = $this->merchantAdsRepository->updateAds($ads, $request);

            if($response['status']) {
                $attribute['content_request'] = [
                    'ID' => $ads->id
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);
            }

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    public function store(MerchantAdsRequest $request)
    {
        if (!MerchantCan::do('ads.edit')) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn này!')];
        } else {
            $response = $this->merchantAdsRepository->storeAds($request);

            if($response['status']){
                $attribute['content_request'] = [
                    'ID' => $response['data']
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);
            }

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    public function destroy($ads, Request $request)
    {
        $ads = $this->merchantAdsRepository->findAdsByID($ads);
        if (!$ads) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Quảng cáo không tồn tại')];
        } else {
            if (!MerchantCan::do('ads.change', $ads)) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn')];
            } else {
                $response = $this->merchantAdsRepository->destroy($ads);

                if($response['status']){
                    $attribute['content_request'] = [
                        'ID' => $ads->id
                    ];
                    $this->logActionMerchantRepository->createAction($request, $attribute);
                }

                return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
            }
        }
    }
}
