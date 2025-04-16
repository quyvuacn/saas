<?php

namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\LogStatusMachine;
use Modules\Merchant\Repositories\LogStatusMachineRepositoryInterface;

class LogStatusMachineRepository extends BaseRepository implements LogStatusMachineRepositoryInterface
{

    public function __construct(LogStatusMachine $model)
    {
        parent::__construct($model);
    }

    public function list()
    {
        $merchant = auth(MERCHANT)->user();
        $endDate = date('Y-m-d H:i:s', strtotime("-30 minute"));
        return $this->model::query()->with(['machine', 'machine.merchant'])
            ->whereHas('machine.merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant->getMerchantID());
            })
            ->where('updated_at', '>=', $endDate)
            ->limit(30)
            ->orderBy('status', 'ASC')
            ->get();
    }
}
