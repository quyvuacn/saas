<?php

namespace Modules\Merchant\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Modules\Merchant\Repositories\AccountRepositoryInterface;
use Modules\Merchant\Repositories\Eloquent\LogActionMerchantRepository;
use Modules\Merchant\Repositories\Eloquent\LogStatusMachineRepository;
use Modules\Merchant\Repositories\Eloquent\MerchantAdsRepository;
use Modules\Merchant\Repositories\Eloquent\MerchantNotificationsRepository;
use Modules\Merchant\Repositories\Eloquent\MerchantSettingStaffRepository;
use Modules\Merchant\Repositories\Eloquent\ProductListRepository;
use Modules\Merchant\Repositories\Eloquent\ProductRepository;
use Modules\Merchant\Repositories\Eloquent\UserDebtRepository;
use Modules\Merchant\Repositories\LogActionMerchantRepositoryInterface;
use Modules\Merchant\Repositories\LogStatusMachineRepositoryInterface;
use Modules\Merchant\Repositories\MerchantAdsRepositoryInterface;
use Modules\Merchant\Repositories\MerchantNotificationsRepositoryInterface;
use Modules\Merchant\Repositories\MerchantSettingStaffInterface;
use Modules\Merchant\Repositories\ProductListRepositoryInterface;
use Modules\Merchant\Repositories\ProductRepositoryInterface;
use Modules\Merchant\Repositories\UserCoinRequestRepositoryInterface;
use Modules\Merchant\Repositories\Eloquent\AccountRepository;
use Modules\Merchant\Repositories\Eloquent\BaseRepository;
use Modules\Merchant\Repositories\Eloquent\UserCoinRequestRepository;
use Modules\Merchant\Repositories\Eloquent\MachineRepository;
use Modules\Merchant\Repositories\Eloquent\MerchantRequestMachineRepository;
use Modules\Merchant\Repositories\Eloquent\RoleRepository;
use Modules\Merchant\Repositories\Eloquent\SubscriptionRepository;
use Modules\Merchant\Repositories\Eloquent\UserHistoryPaymentRepository;
use Modules\Merchant\Repositories\Eloquent\UserRepository;
use Modules\Merchant\Repositories\EloquentRepositoryInterface;
use Modules\Merchant\Repositories\MachineRepositoryInterface;
use Modules\Merchant\Repositories\MerchantRequestMachineRepositoryInterface;
use Modules\Merchant\Repositories\RoleRepositoryInterface;
use Modules\Merchant\Repositories\SubscriptionRepositoryInterface;
use Modules\Merchant\Repositories\UserDebtRepositoryInterface;
use Modules\Merchant\Repositories\UserHistoryPaymentRepositoryInterface;
use Modules\Merchant\Repositories\UserRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserCoinRequestRepositoryInterface::class, UserCoinRequestRepository::class);
        $this->app->bind(UserHistoryPaymentRepositoryInterface::class, UserHistoryPaymentRepository::class);
        $this->app->bind(MachineRepositoryInterface::class, MachineRepository::class);
        $this->app->bind(MerchantRequestMachineRepositoryInterface::class, MerchantRequestMachineRepository::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductListRepositoryInterface::class, ProductListRepository::class);
        $this->app->bind(MerchantSettingStaffInterface::class, MerchantSettingStaffRepository::class);
        $this->app->bind(MerchantAdsRepositoryInterface::class, MerchantAdsRepository::class);
        $this->app->bind(LogActionMerchantRepositoryInterface::class, LogActionMerchantRepository::class);
        $this->app->bind(LogStatusMachineRepositoryInterface::class, LogStatusMachineRepository::class);
        $this->app->bind(MerchantNotificationsRepositoryInterface::class, MerchantNotificationsRepository::class);
        $this->app->bind(UserDebtRepositoryInterface::class, UserDebtRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
