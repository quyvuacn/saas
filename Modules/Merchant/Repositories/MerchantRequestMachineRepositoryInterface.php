<?php


namespace Modules\Merchant\Repositories;


interface MerchantRequestMachineRepositoryInterface
{
    public function requestMachine($request);

    public function requestHistory($request);

    public function merchantMachines($merchant_id, $new = true);

    public function findNewRequestByID($request_id);

    public function deleteRequest($request);

    public function updateRequest($request, $requestChanges);
}
