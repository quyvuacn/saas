<?php

namespace Modules\Admin\Repositories;

interface SubscriptionHistoryRepositoryInterface
{
    public function list($request, $merchantId);

    public function createSubscriptionHistory($arrAttributeSubscription, $dateExpireBegin, $dateExpireEnd);
}
