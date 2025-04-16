<?php

namespace Modules\Admin\Repositories;

interface MachineAttributeRepositoryInterface
{
    public function list($request);

    public function findByIds($attributeIds);

    public function createAttribute($attributeName, $attributesValue);
}
