<?php

namespace Modules\Admin\Repositories;

interface MerchantRepositoryInterface
{
    public function list($request);

    public function findMerchantActive();

    public function findMerchantParent();

    public function findMerchantNotActive();

    public function getSubscription();

    public function updateMachineCount($merchantId);

    public function approveMerchant($merchantRequest, $request);

    public function finalApproveMerchant($merchant);

    public function findMerchantInfoActive($merchantId);

    public function updateMerchantInfo($merchant, $request);

    public function getTotalMerchantRequest();
}
