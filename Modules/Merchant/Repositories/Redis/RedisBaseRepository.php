<?php

namespace Modules\Merchant\Repositories\Eloquent;

use Modules\Merchant\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

// ONLY EXAMPLE
class RedisBaseRepository implements EloquentRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
    }

    public function all()
    {
    }

    public function create(array $attributes)
    {
    }

    public function find($id)
    {
    }

    public function update($id, array $attributes)
    {

    }

    public function delete($id)
    {

    }
}
