<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Merchant;
use App\Models\MerchantInfo;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\MachineRepositoryInterface;
use Modules\Admin\Repositories\MerchantInfoRepositoryInterface;
use Modules\Admin\Repositories\MerchantRepositoryInterface;

class MerchantRepository extends BaseRepository implements MerchantRepositoryInterface
{
    protected $merchantInfoRepository;

    protected $machineRepository;

    public function __construct(
        Merchant $model,
        MerchantInfoRepositoryInterface $merchantInfoRepository,
        MachineRepositoryInterface $machineRepository
    )
    {
        parent::__construct($model);
        $this->merchantInfoRepository = $merchantInfoRepository;
        $this->machineRepository = $machineRepository;
    }

    public function list($request)
    {
        // TODO: Implement list() method.
    }

    public function findMerchantActive()
    {
        $result = $this->model->where('status', $this->model::REQUEST_SUCCESS);
        return $result;
    }


    public function findMerchantParent()
    {
        $result = $this->model->where('status', $this->model::REQUEST_SUCCESS)
            ->where('parent_id', 0);
        return $result;
    }

    public function findMerchantNotActive()
    {
        $result = $this->model->where('status', '!=', $this->model::REQUEST_SUCCESS)
            ->orderBy('created_at', 'desc');
        return $result;
    }

    public function getSubscription()
    {
        $result = $this->model->select(['merchant.*', DB::raw('COUNT(subscription.merchant_id) as scount')])
            ->join('subscription', 'merchant.id', '=', 'subscription.merchant_id')
            ->groupBy('subscription.merchant_id')
            ->where('merchant.status', $this->model::REQUEST_SUCCESS)
            ->having('scount', '>', 0)
            ->with(['subscription', 'subscription.machineSubscription'])
            ->get();
        return $result;
    }

    public function updateMachineCount($merchantId)
    {
        $merchant = $this->find($merchantId);
        $machineCount = $this->machineRepository->getCountMachineByMerchantId($merchantId);
        $merchant->machine_count = $machineCount;
        return $merchant->save();
    }

    public function approveMerchant($merchantRequest, $request)
    {
        $merchantInfo = $this->merchantInfoRepository->find($merchantRequest->id);
        $merchantInfoAttribute = [];
        if (empty($merchantInfo)) {
            $merchantInfoAttribute['merchant_id'] = $merchantRequest->id;
        }
        $merchantRequest->updated_by = auth('admin')->user()->id;
        if ($merchantRequest->status == $merchantRequest::REQUEST_WAITING) {
            $merchantRequest->status = ($request->merchant_audit == 1) ? $merchantRequest::REQUEST_WAITING_SETUP : $merchantRequest::REQUEST_CANCEL;
        } else {

            $merchantRequest->status = ($request->merchant_audit == 1) ? $merchantRequest::REQUEST_WAITING : $merchantRequest::REQUEST_CANCEL;
            $merchantParent = $this->model->find($merchantRequest->created_by);
            if (!empty($merchantParent)) {
                $merchantRequest->parent_id = $merchantParent->id;
            }
        }
        if ($request->merchant_audit == 1) {
            $merchantRequest->machine_count = $request->machine_count;
            $merchantInfoAttribute['merchant_active_date'] = convertDateFlatpickr($request->merchant_active_date);
            $merchantInfoAttribute['merchant_other_request'] = $request->merchant_other_request;
        } else {
            $merchantInfoAttribute['merchant_cancel_reason'] = $request->merchant_cancel_reason;
        }

        $merchantRequest->save();

        if(empty($merchantInfo)){
            $this->merchantInfoRepository->create($merchantInfoAttribute);
        } else {
            $merchantInfo->update($merchantInfoAttribute);
        }
    }

    public function finalApproveMerchant($merchant)
    {
        $merchant->status = $this->model::REQUEST_SUCCESS;
        $merchant->machine_count = 0;
        $merchant->updated_by = auth('admin')->user()->id;
        return $merchant->save();
    }

    public function findMerchantInfoActive($merchantId)
    {
        $result = $this->model->where('id', $merchantId)
            ->where('status', $this->model::REQUEST_SUCCESS)
            ->where('is_deleted', '!=', $this->model::DELETED)
            ->with('merchantInfo')
            ->first();
        return $result;
    }

    public function updateMerchantInfo($merchant, $request)
    {
        $merchant->name = $request->name;
        $merchant->email = $request->email;
        $merchant->phone = $request->phone;
        $merchant->save();
        if($merchant->parent_id != 0){
            $merchant = $merchant->commonMerchant();
        }
        $merchant->merchantInfo()->update([
            'merchant_company' => $request->merchant_company,
            'merchant_address' => $request->merchant_address,
        ]);
        return $merchant;
    }

    public function getTotalMerchantRequest()
    {
        $result = $this->model::query()
            ->where('status', $this->model::REQUEST_NEW)
            ->count();
        return $result;
    }

}
