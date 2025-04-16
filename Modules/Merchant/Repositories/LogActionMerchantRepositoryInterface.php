<?php


namespace Modules\Merchant\Repositories;


interface LogActionMerchantRepositoryInterface
{
    public function list($request);

    public function createAction($request, $attribute);
}
