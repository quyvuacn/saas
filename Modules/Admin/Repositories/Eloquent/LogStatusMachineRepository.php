<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\LogStatusMachine;
use Modules\Admin\Repositories\LogStatusMachineRepositoryInterface;

class LogStatusMachineRepository extends BaseRepository implements LogStatusMachineRepositoryInterface
{

    public function __construct(LogStatusMachine $model)
    {
        parent::__construct($model);
    }

    public function list()
    {
        $endDate = date('Y-m-d H:i:s', strtotime("-30 minute"));
        $result = $this->model::query()
            ->where('updated_at', '>=', $endDate)
            ->limit(30)
            ->orderBy('status', 'ASC')
            ->get();
        return $result;
    }
}
