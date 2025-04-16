<?php


namespace Modules\Merchant\Repositories;


interface MachineRepositoryInterface
{
    public function list($request);

    public function history($request);

    public function findMachineOfMerchantByID($machine_id, $merchant_id);

    public function changeAddress($machine, $request);

    public function sync();

    public function requestBack($machine, $request);

    public function listActiveMachines();

    public function getProductOnMachines();
}
