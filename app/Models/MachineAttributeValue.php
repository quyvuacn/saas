<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineAttributeValue extends Model
{
    protected $table = 'machine_attribute_value';
    protected $guarded = [];

    const DELETED = 1;

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)
            ->where('is_deleted', '!=', self::DELETED);
    }

    public function attribute()
    {
        return $this->belongsTo('App\Models\MachineAttribute');
    }
}
