<?php

namespace Modules\Admin\Repositories;

interface ProductListRepositoryInterface
{
    public function createProductList($machine, $request);

    public function updateProductList($machine, $request);

    public function createPack($attribute);

    public function updatePack($productListId, $attribute);

    public function getListByMachineId($machineId);
}
