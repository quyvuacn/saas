<?php

namespace Modules\Admin\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Admin\Repositories\AdminRepositoryInterface;
use Modules\Admin\Repositories\AppVersionRepositoryInterface;
use Modules\Admin\Repositories\Eloquent\AdminRepository;
use Modules\Admin\Repositories\Eloquent\AppVersionRepository;
use Modules\Admin\Repositories\Eloquent\BaseRepository;
use Modules\Admin\Repositories\Eloquent\LogActionAdminRepository;
use Modules\Admin\Repositories\Eloquent\LogStatusMachineRepository;
use Modules\Admin\Repositories\Eloquent\MachineAttributeRepository;
use Modules\Admin\Repositories\Eloquent\MachineAttributeValueRepository;
use Modules\Admin\Repositories\Eloquent\MachineRepository;
use Modules\Admin\Repositories\Eloquent\MachineRequestBackRepository;
use Modules\Admin\Repositories\Eloquent\MerchantInfoRepository;
use Modules\Admin\Repositories\Eloquent\MerchantRepository;
use Modules\Admin\Repositories\Eloquent\MerchantRequestMachineRepository;
use Modules\Admin\Repositories\Eloquent\ProductListRepository;
use Modules\Admin\Repositories\Eloquent\PermissionRepository;
use Modules\Admin\Repositories\Eloquent\ReportStatusMachineRepository;
use Modules\Admin\Repositories\Eloquent\RoleRepository;
use Modules\Admin\Repositories\Eloquent\SubscriptionHistoryRepository;
use Modules\Admin\Repositories\Eloquent\SubscriptionRepository;
use Modules\Admin\Repositories\Eloquent\SubscriptionRequestRepository;
use Modules\Admin\Repositories\EloquentRepositoryInterface;
use Modules\Admin\Repositories\LogActionAdminRepositoryInterface;
use Modules\Admin\Repositories\LogStatusMachineRepositoryInterface;
use Modules\Admin\Repositories\MachineAttributeRepositoryInterface;
use Modules\Admin\Repositories\MachineAttributeValueRepositoryInterface;
use Modules\Admin\Repositories\MachineRepositoryInterface;
use Modules\Admin\Repositories\MachineRequestBackRepositoryInterface;
use Modules\Admin\Repositories\MerchantInfoRepositoryInterface;
use Modules\Admin\Repositories\MerchantRepositoryInterface;
use Modules\Admin\Repositories\MerchantRequestMachineRepositoryInterface;
use Modules\Admin\Repositories\ProductListRepositoryInterface;
use Modules\Admin\Repositories\PermissionRepositoryInterface;
use Modules\Admin\Repositories\ReportStatusMachineRepositoryInterface;
use Modules\Admin\Repositories\RoleRepositoryInterface;
use Modules\Admin\Repositories\SubscriptionHistoryRepositoryInterface;
use Modules\Admin\Repositories\SubscriptionRepositoryInterface;
use Modules\Admin\Repositories\SubscriptionRequestRepositoryInterface;

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
        $this->app->bind(MachineRepositoryInterface::class, MachineRepository::class);
        $this->app->bind(MerchantRepositoryInterface::class, MerchantRepository::class);
        $this->app->bind(MachineAttributeRepositoryInterface::class, MachineAttributeRepository::class);
        $this->app->bind(MachineAttributeValueRepositoryInterface::class, MachineAttributeValueRepository::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->bind(SubscriptionRequestRepositoryInterface::class, SubscriptionRequestRepository::class);
        $this->app->bind(SubscriptionHistoryRepositoryInterface::class, SubscriptionHistoryRepository::class);
        $this->app->bind(MerchantRequestMachineRepositoryInterface::class, MerchantRequestMachineRepository::class);
        $this->app->bind(MachineRequestBackRepositoryInterface::class, MachineRequestBackRepository::class);
        $this->app->bind(MerchantInfoRepositoryInterface::class, MerchantInfoRepository::class);
        $this->app->bind(ProductListRepositoryInterface::class, ProductListRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(LogActionAdminRepositoryInterface::class, LogActionAdminRepository::class);
        $this->app->bind(ReportStatusMachineRepositoryInterface::class, ReportStatusMachineRepository::class);
        $this->app->bind(LogStatusMachineRepositoryInterface::class, LogStatusMachineRepository::class);
        $this->app->bind(AppVersionRepositoryInterface::class, AppVersionRepository::class);
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
