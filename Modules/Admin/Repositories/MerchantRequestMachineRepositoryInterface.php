<?php

namespace Modules\Admin\Repositories;

interface MerchantRequestMachineRepositoryInterface
{
    public function list($request);

    public function finalUpdateMerchantRequestMachine($obj);

    public function finalMerchantRequestMachineProcessing($obj);

    public function updateMerchantRequestMachine($obj, $request);

    public function findRequest($requestId);

    public function getListRequestWaitingApprove();

    public function getTotalRequet();

    public function getListRequestNew();
}
