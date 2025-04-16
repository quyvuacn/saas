<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Repositories\UserCoinRequestRepositoryInterface;

class ClearCoinRequestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coin_request:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa yêu cầu nạp tiền sau 24h nếu user không chuyển khoản, hoặc không được Merchant xác nhận';

    protected $userCoinRequestRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserCoinRequestRepositoryInterface $userCoinRequestRepository)
    {
        parent::__construct();
        $this->userCoinRequestRepository = $userCoinRequestRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            echo 'Clear Coin Request after 24h start ========================<br>';
            Log::info(' Clear Coin Request after 24h start ========================');
            $this->userCoinRequestRepository->getUnApproveCoinRequests()->chunkById(30, function ($coinRequests) {
                $coinRequestIds = $coinRequests->pluck('id')->toArray();
                $this->userCoinRequestRepository->clearUnApproveCoinRequests($coinRequestIds);
                if (env('APP_ENV') === 'local') {
                    echo round(memory_get_usage() / 1024000) . ' M - ';
                }
            });
            $this->info('Clear Coin Request after 24h successfully =================');
            echo '<br>Clear Coin Request after 24h successfully =================';
        } catch (\Exception $e) {
            Log::info('User Debt report Errors');
            Log::info($e->getMessage());
            $this->info($e->getMessage());
        }
    }
}
