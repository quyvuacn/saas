<?php

namespace Modules\Admin\Repositories;

interface MachineAttributeValueRepositoryInterface
{
    public function list($request);

    public function findAttributeValueByMachine($machineId);

    public function deleteAttributeValueByMachineId($machineId);

    public function createAttributeValue($obj, $arrAttribute, $arrValue);
}
