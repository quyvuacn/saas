<?php

namespace Modules\Admin\Repositories;

interface MachineRepositoryInterface
{
    public function list($request);

    public function findMachineActive();

    public function findMachineAvailiable();

    public function updateMachine($machine, $request, $arrAttr);

    public function createMachine($request, $arrAttr);

    public function deleteMachine($machine);

    public function getMaxId();

    public function removeMerchant($machineId);

    public function getTotalGroupByStatus();

    public function getAllStatusName();

    public function getStatusName($status);

    public function changeDeviceID($machine, $request);

    public function getCountMachineByMerchantId($merchantId);

    public function checkExitsDeviceId($deviceId);
}
