<?php

namespace Modules\Admin\Repositories;

interface LogActionAdminRepositoryInterface
{
    public function createAction($request, $attribute);

    public function list($request);
}
