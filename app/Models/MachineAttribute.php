<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineAttribute extends Model
{
    protected $table = 'machine_attribute';
    protected $guarded = [];

    const DELETED = 1;


    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)
            ->where('is_deleted', '!=', self::DELETED);
    }

    public function attributesValue(){
        return $this->hasOne('machine_attribute_value', 'attribute_id');
    }
}
