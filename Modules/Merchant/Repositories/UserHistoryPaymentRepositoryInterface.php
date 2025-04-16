<?php


namespace Modules\Merchant\Repositories;


use App\Models\UserCoinRequest;

interface UserHistoryPaymentRepositoryInterface
{
    public function createHistoryPayment($coin, $request);

    public function latestSellingTransactions($limit = 10);

    public function getLatestWeekRevenue();

    public function getTotalTodayRevenue();
}
