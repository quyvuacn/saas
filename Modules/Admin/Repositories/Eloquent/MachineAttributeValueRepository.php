<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\MachineAttributeValue;
use Modules\Admin\Repositories\MachineAttributeValueRepositoryInterface;

class MachineAttributeValueRepository extends BaseRepository implements MachineAttributeValueRepositoryInterface
{
    public function __construct(MachineAttributeValue $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        // TODO: Implement list() method.
    }

    public function findAttributeValueByMachine($machineId)
    {
        return $this->model->where('machine_id', $machineId)->get();
    }

    public function createAttributeValue($obj, $arrAttribute, $arrValue)
    {
        $attribute = [];
        foreach ($arrAttribute as $k => $v) {
            if (empty($arrValue[$k]))
                continue;
            $attribute[] = [
                'attribute_id' => $k,
                'attribute_value' => $arrValue[$k],
                'created_by' => auth('admin')->user()->id
            ];
        }
        return $obj->attributeValues()->createMany($attribute);
    }

    public function deleteAttributeValueByMachineId($machineId)
    {
        return $this->model->where('machine_id', $machineId)
            ->update(['is_deleted' => self::IS_DELETED]);
    }
}
