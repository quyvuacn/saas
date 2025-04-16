<?php


namespace Modules\Merchant\Repositories;


interface SubscriptionRepositoryInterface
{
    public function list($request);

    public function history($request);

    public function findSubscriptionByID($id);

    public function extend($subscription, $request);
}
