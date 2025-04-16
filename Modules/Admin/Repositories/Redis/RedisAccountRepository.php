<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Merchant;
use Modules\Merchant\Repositories\AccountRepositoryInterface;

// ONLY EXAMPLE
class RedisAccountRepository extends RedisBaseRepository implements AccountRepositoryInterface
{
    public function __construct(Merchant $model)
    {
        parent::__construct($model);
    }

    public function more()
    {
        return $this->model->all();
    }
}
