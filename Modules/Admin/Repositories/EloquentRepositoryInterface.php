<?php


namespace Modules\Admin\Repositories;


interface EloquentRepositoryInterface
{
    public function all();

    public function create(array $attributes);

    public function find($id);

    public function updateById($id, array $attributes);

    public function delete($id);
}
