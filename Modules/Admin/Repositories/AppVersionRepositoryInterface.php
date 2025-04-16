<?php


namespace Modules\Admin\Repositories;


interface AppVersionRepositoryInterface
{
    public function list($request);

    public function store($request);
}
