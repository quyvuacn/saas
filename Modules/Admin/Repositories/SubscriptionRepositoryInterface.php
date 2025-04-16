<?php

namespace Modules\Admin\Repositories;

interface SubscriptionRepositoryInterface
{
    public function list($request);

    public function isEditMachine($machine);

    public function findBySubscriptionRequest($merchantId, $machineId);

    public function updateSubscription($obj, $dateExpire);

    public function createSubscription($attributeSubscription);

    public function updateSubscriptionBySubscriptionRequest($subscription, $attributeSubscription);

    public function getSubscriptionAboutToExpire();

    public function expireSubscription($machineId, $merchantId);

    public function getSubscriptionExpireAndMachineNotBack();
}
