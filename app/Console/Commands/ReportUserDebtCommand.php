<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Repositories\Eloquent\UserHistoryPaymentRepository;
use Modules\Merchant\Repositories\UserDebtRepositoryInterface;
use Modules\Merchant\Repositories\UserHistoryPaymentRepositoryInterface;
use Modules\Merchant\Repositories\UserRepositoryInterface;

class ReportUserDebtCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:user_debt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create report User debt monthly';

    protected $userRepository;
    protected $userHistoryPaymentRepository;
    protected $userDebtRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        UserHistoryPaymentRepositoryInterface $userHistoryPaymentRepository,
        UserRepositoryInterface $userRepository,
        UserDebtRepositoryInterface $userDebtRepository
    ) {
        parent::__construct();
        $this->userRepository               = $userRepository;
        $this->userHistoryPaymentRepository = $userHistoryPaymentRepository;
        $this->userDebtRepository           = $userDebtRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            echo 'User Debt report start ========================<br>';
            Log::info(' User Debt report start ========================');
            $this->userRepository->getDebtUsers()->chunkById(30, function ($debtUsers) {
                foreach ($debtUsers as $debtUser) {
                    $this->userDebtRepository->createDebtReport($debtUser);
                    if (env('APP_ENV') === 'local') {
                        echo round(memory_get_usage() / 1024000) . ' M - ';
                    }
                }
            });
            $this->info('User Debt report successfully =================');
            echo '<br>User Debt report successfully =================';
        } catch (\Exception $e) {
            Log::info('User Debt report Errors');
            Log::info($e->getMessage());
            $this->info($e->getMessage());
        }
    }
}
