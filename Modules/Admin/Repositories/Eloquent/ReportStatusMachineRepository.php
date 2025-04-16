<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\ReportStatusMachine;
use Modules\Admin\Repositories\ReportStatusMachineRepositoryInterface;

class ReportStatusMachineRepository extends BaseRepository implements ReportStatusMachineRepositoryInterface
{

    public function __construct(ReportStatusMachine $model) {
        parent::__construct($model);
    }

    public function getDataChartActive()
    {
//        $ddate = date('Y-m-d');
//        $date = new \DateTime($ddate);
//        $week = $date->format("W");
//
//        $firstKey = $week . '_' . $date->format("Y");
//        $arrWeek = [$firstKey];
//        $start = $week - 1;
//        if($week <= 6){
//            $lastYear = $date->format("Y") - 1;
//            $ydate = new \DateTime($lastYear . '-12-28');
//            $lastWeekLastYear = $ydate->format("W");
//            for ($i = 0; $i < 5; $i++){
//                if($start >= 1){
//                    $arrWeek[] = str_pad($start--, 2, '0', STR_PAD_LEFT) . '_' . $date->format("Y");
//                } else {
//                    $arrWeek[] = str_pad($lastWeekLastYear--, 2, '0') . '_' . $lastYear;
//                }
//            }
//        } else {
//            for ($i = 0; $i < 5; $i++){
//                $arrWeek[] = $start-- . '_' . $date->format("Y");
//            }
//        }

//        $data = $this->model::query()
//            ->whereIn('week_year', $arrWeek)
//            ->where('status', $this->model::MACHINE_WAS_GRANTED)
//            ->get();

        $data = $this->model::query()
            ->where('status', $this->model::MACHINE_WAS_GRANTED)
            ->orderBy('created_at', 'DESC')
            ->limit(6)
            ->get();

        return $data;
    }
}
