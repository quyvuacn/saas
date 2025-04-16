<?php

namespace App\Console\Commands;

use App\Models\Machine;
use App\Models\ReportStatusMachine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReportMachineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:machine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create report status machine';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo $this->updateReportMachine();
        die();
    }

    public function updateReportMachine()
    {
        $ddate = new \DateTime(date('Y-m-d'));
        $key = $ddate->format('W') . '_' . $ddate->format('Y');

        try {
            $data = Machine::getCountByStatus();
            $arrStatus = array_keys(Machine::STATUS_NAME);
            $dataConvert = array_combine(array_column($data, 'status'), array_column($data, 'cid'));

            foreach ($arrStatus as $status){
                $model = new ReportStatusMachine();
                $model->week_year = $key;
                $model->status = $status;
                $model->data = $dataConvert[$status] ?? 0;
                $model->created_at = date('Y-m-d H:i:s');
                $model->save();
            }
            return 'done';
        } catch (\Exception $exception) {
            Log::error('[ReportMachineCommand][createReport]--' . $exception->getMessage());
            return 'error';
        }
    }
}
