<?php

namespace Modules\Merchant\Providers;

use App\Merchant;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Merchant\Policies\UserPolicy;

class MerchantAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\User' => 'Modules\Merchant\Policies\UserPolicy',
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        // Dashboard
        Gate::define('isApproved', '\Modules\Merchant\Policies\ApprovedPolicy@isApproved');
        Gate::define('dashboard', '\Modules\Merchant\Policies\DashboardPolicy@viewDashboard');
        // Customer
        Gate::define('user.list', '\Modules\Merchant\Policies\UserPolicy@list');
        Gate::define('user.edit', '\Modules\Merchant\Policies\UserPolicy@edit');
        Gate::define('user.change', '\Modules\Merchant\Policies\UserPolicy@change');

        // Customer Credit
        Gate::define('user.credit.list', '\Modules\Merchant\Policies\UserCreditPolicy@list');
        Gate::define('user.credit.edit', '\Modules\Merchant\Policies\UserCreditPolicy@edit');
        Gate::define('user.credit.change', '\Modules\Merchant\Policies\UserCreditPolicy@change');

        // Customer Debt
        Gate::define('user.debt.list', '\Modules\Merchant\Policies\UserDebtPolicy@list');
        Gate::define('user.debt.edit', '\Modules\Merchant\Policies\UserDebtPolicy@edit');
        Gate::define('user.debt.change', '\Modules\Merchant\Policies\UserDebtPolicy@change');

        // Customer Debt
        Gate::define('user.coin.request.list', '\Modules\Merchant\Policies\UserCoinRequestPolicy@list');
        Gate::define('user.coin.request.edit', '\Modules\Merchant\Policies\UserCoinRequestPolicy@edit');
        Gate::define('user.coin.request.change', '\Modules\Merchant\Policies\UserCoinRequestPolicy@change');

        // Merchant Account
        Gate::define('account.list', '\Modules\Merchant\Policies\AccountPolicy@list');
        Gate::define('account.edit', '\Modules\Merchant\Policies\AccountPolicy@edit');
        Gate::define('account.change', '\Modules\Merchant\Policies\AccountPolicy@change');

        // Account History
        Gate::define('account.history', '\Modules\Merchant\Policies\AccountHistoryPolicy@list');

        // Permission
        Gate::define('permission.list', '\Modules\Merchant\Policies\PermissionPolicy@list');
        Gate::define('permission.edit', '\Modules\Merchant\Policies\PermissionPolicy@edit');
        Gate::define('permission.change', '\Modules\Merchant\Policies\PermissionPolicy@change');

        // Machine
        Gate::define('machine.list', '\Modules\Merchant\Policies\MachinePolicy@list');
        Gate::define('machine.edit', '\Modules\Merchant\Policies\MachinePolicy@edit');
        Gate::define('machine.change', '\Modules\Merchant\Policies\MachinePolicy@change');
        Gate::define('machine.changeRequest', '\Modules\Merchant\Policies\MachinePolicy@changeRequest');

        Gate::define('machine.hasAny', '\Modules\Merchant\Policies\HasMachinePolicy@hasAnyMachine');

        // Subscription
        Gate::define('subscription.list', '\Modules\Merchant\Policies\SubscriptionPolicy@list');
        Gate::define('subscription.edit', '\Modules\Merchant\Policies\SubscriptionPolicy@edit');
        Gate::define('subscription.change', '\Modules\Merchant\Policies\SubscriptionPolicy@change');

        // Subscription History
        Gate::define('subscription.history.list', '\Modules\Merchant\Policies\SubscriptionHistoryPolicy@list');

        // Selling History
        Gate::define('selling.history.list', '\Modules\Merchant\Policies\SellingHistoryPolicy@list');

        // Machine Request History
        Gate::define('machine_request.history.list', '\Modules\Merchant\Policies\MachineRequestHistoryPolicy@list');

        // Machine Request
        Gate::define('machine_request.list', '\Modules\Merchant\Policies\MachineRequestPolicy@list');
        Gate::define('machine_request.edit', '\Modules\Merchant\Policies\MachineRequestPolicy@edit');
        Gate::define('machine_request.change', '\Modules\Merchant\Policies\MachineRequestPolicy@change');

        // Product
        Gate::define('product.list', '\Modules\Merchant\Policies\ProductPolicy@list');
        Gate::define('product.edit', '\Modules\Merchant\Policies\ProductPolicy@edit');
        Gate::define('product.change', '\Modules\Merchant\Policies\ProductPolicy@change');

        // Ads
        Gate::define('ads.list', '\Modules\Merchant\Policies\MerchantAdsPolicy@list');
        Gate::define('ads.edit', '\Modules\Merchant\Policies\MerchantAdsPolicy@edit');
        Gate::define('ads.change', '\Modules\Merchant\Policies\MerchantAdsPolicy@change');

        Gate::define('setting.edit', '\Modules\Merchant\Policies\SettingPolicy@edit');

        // Notify
        Gate::define('notify.list', '\Modules\Merchant\Policies\NotifyPolicy@list');
        Gate::define('notify.edit', '\Modules\Merchant\Policies\NotifyPolicy@edit');
        Gate::define('notify.change', '\Modules\Merchant\Policies\NotifyPolicy@change');

        // Product Selling
        Gate::define('product.selling.list', '\Modules\Merchant\Policies\ProductSellingPolicy@list');
        Gate::define('product.selling.edit', '\Modules\Merchant\Policies\ProductSellingPolicy@edit');
        Gate::define('product.selling.change', '\Modules\Merchant\Policies\ProductSellingPolicy@change');

        // Product Sync
        Gate::define('product.sync.list', '\Modules\Merchant\Policies\ProductSyncPolicy@list');
        Gate::define('product.sync.edit', '\Modules\Merchant\Policies\ProductSyncPolicy@edit');
        Gate::define('product.sync.change', '\Modules\Merchant\Policies\ProductSyncPolicy@change');
    }
}
