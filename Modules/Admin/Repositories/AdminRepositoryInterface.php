<?php


namespace Modules\Admin\Repositories;


interface AdminRepositoryInterface
{
    public function findById($adminId);

    public function list($request);
}
