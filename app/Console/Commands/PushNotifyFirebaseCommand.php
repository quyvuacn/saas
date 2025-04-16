<?php

namespace App\Console\Commands;

use App\Models\Machine;
use App\Models\ReportStatusMachine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Repositories\MerchantNotificationsRepositoryInterface;
use Modules\Merchant\Repositories\UserRepositoryInterface;

class PushNotifyFirebaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:firebase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push notify firebase to user merchant';

    protected $merchantNotifyRepository;

    protected $userRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        MerchantNotificationsRepositoryInterface $merchantNotificationsRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->merchantNotifyRepository = $merchantNotificationsRepository;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notify = $this->isPushNotify();
        echo $this->pushNotify($notify);
    }

    public function pushNotify($notify)
    {
        try {
            foreach ($notify as $noti){
                $listUserMerchant = $this->userRepository->getUserByMerchantId($noti->merchant_id);
                $queryString = 'INSERT INTO notification (title, brief, content, uid, merchant_id, status, published_date) VALUES ';
                $arrSql = [];
                foreach ($listUserMerchant as $user){
                    if(empty($user->firebase_token))
                        continue;

                    callApiNotifyFirebase($noti->title, $noti->brief, $user->firebase_token);

                    $arrSql[] = strtr("('$1','$2','$3','$4',$5,$6,'$7')", [
                        '$1' => $noti->title,
                        '$2' => $noti->brief,
                        '$3' => $noti->content,
                        '$4' => $user->uid,
                        '$5' => $noti->merchant_id,
                        '$6' => 2,
                        '$7' => date('Y-m-d H:i:s')
                    ]);
                }
                if(count($arrSql >= 50)){
                    DB::statement($queryString . implode(',', $arrSql));
                    $arrSql = [];
                }
                $noti->update([
                    'status' => $noti::READED
                ]);

            }
            if(!empty($arrSql)){
                DB::statement($queryString . implode(';', $arrSql));
            }
            return 'done';
        } catch (\Exception $exception) {
            Log::error('[PushNotifyFirebaseCommand]--' . $exception->getMessage());
            return 'error';
        }
    }

    public function  isPushNotify()
    {
        $notify = $this->merchantNotifyRepository->isPushNotify();
        return $notify;
    }
}
