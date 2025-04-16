<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\Machine;
use App\Models\MachineAttribute;
use Modules\Admin\Repositories\MachineAttributeRepositoryInterface;
use Modules\Admin\Repositories\MachineRepositoryInterface;
use Yajra\DataTables\DataTables;

class MachineAttributeRepository extends BaseRepository implements MachineAttributeRepositoryInterface
{
    public function __construct(MachineAttribute $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        // TODO: Implement list() method.
    }

    public function findByIds($attributeIds)
    {
        return $this->model->whereIn('id', $attributeIds)->get()->toArray();
    }

    public function createAttribute($attributeName, $attributesValue)
    {
        return $this->create([
            'attribute_name'    => $attributeName,
            'value_default'     => $attributesValue,
            'created_by'        => auth('admin')->user()->id,
            'updated_by'        => auth('admin')->user()->id
        ]);
    }
}
