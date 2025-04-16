<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\MerchantInfo;
use Modules\Admin\Repositories\MerchantInfoRepositoryInterface;

class MerchantInfoRepository extends BaseRepository implements MerchantInfoRepositoryInterface
{
    public function __construct(MerchantInfo $model)
    {
        parent::__construct($model);
    }
}
