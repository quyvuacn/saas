<?php


namespace Modules\Merchant\Repositories;


interface ProductListRepositoryInterface
{
    public function findPacksOfTrays($machine_id, $tray_ids);

    public function findPackOfMerchantByID($pack_id);

    public function togglePack($pack);

    public function updateMachineProducts($machine, $request, $merchantProducts);

    public function getListByMachineId($machineId);
}
