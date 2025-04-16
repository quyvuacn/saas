<?php

namespace Modules\Admin\Providers;

use App\Admin;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Admin\Policies\AccountPolicy;

class AdminAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\User' => 'Modules\Admin\Policies\AccountPolicy',
        // User::class => UserPolicy::class,
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
        Gate::define('adm.dashboard', '\Modules\Admin\Policies\DashboardPolicy@viewDashboard');
        // // Customer
        Gate::define('adm.user.list', '\Modules\Admin\Policies\UserPolicy@list');
        Gate::define('adm.user.edit', '\Modules\Admin\Policies\UserPolicy@edit');
        Gate::define('adm.user.change', '\Modules\Admin\Policies\UserPolicy@change');
        Gate::define('adm.user.create', '\Modules\Admin\Policies\UserPolicy@create');
        Gate::define('adm.user.show', '\Modules\Admin\Policies\UserPolicy@show');

        // Admin Account
        Gate::define('adm.account.list', '\Modules\Admin\Policies\AccountPolicy@list');
        Gate::define('adm.account.edit', '\Modules\Admin\Policies\AccountPolicy@edit');
        Gate::define('adm.account.change', '\Modules\Admin\Policies\AccountPolicy@change');
        Gate::define('adm.account.create', '\Modules\Admin\Policies\AccountPolicy@create');
        Gate::define('adm.account.show', '\Modules\Admin\Policies\AccountPolicy@show');
        Gate::define('adm.account.is_super_admin', '\Modules\Admin\Policies\AccountPolicy@isSuperAdmin');
        Gate::define('adm.account.history', '\Modules\Admin\Policies\AccountPolicy@history');

        //Merchant
        Gate::define('adm.merchant.list', '\Modules\Admin\Policies\MerchantPolicy@list');
        Gate::define('adm.merchant.edit', '\Modules\Admin\Policies\MerchantPolicy@edit');
        Gate::define('adm.merchant.change', '\Modules\Admin\Policies\MerchantPolicy@change');
        Gate::define('adm.merchant.show', '\Modules\Admin\Policies\MerchantPolicy@show');
        Gate::define('adm.merchant_request.list', '\Modules\Admin\Policies\MerchantPolicy@request');
        Gate::define('adm.merchant_request.edit', '\Modules\Admin\Policies\MerchantPolicy@approve');

        // Machine
        Gate::define('adm.machine.list', '\Modules\Admin\Policies\MachinePolicy@list');
        Gate::define('adm.machine.edit', '\Modules\Admin\Policies\MachinePolicy@edit');
        Gate::define('adm.machine.change', '\Modules\Admin\Policies\MachinePolicy@change');
        Gate::define('adm.machine.create', '\Modules\Admin\Policies\MachinePolicy@create');
        Gate::define('adm.machine.show', '\Modules\Admin\Policies\MachinePolicy@show');
        Gate::define('adm.machine.processing', '\Modules\Admin\Policies\MachinePolicy@processing');
        Gate::define('adm.machine.app_version_edit', '\Modules\Admin\Policies\MachinePolicy@appVersionEdit');
        Gate::define('adm.machine.app_version_list', '\Modules\Admin\Policies\MachinePolicy@appVersionList');
        Gate::define('adm.machine_request.list', '\Modules\Admin\Policies\MachinePolicy@request');
        Gate::define('adm.machine_request.edit', '\Modules\Admin\Policies\MachinePolicy@approveRequest');
        Gate::define('adm.machine_request_back.list', '\Modules\Admin\Policies\MachinePolicy@requestBack');
        Gate::define('adm.machine_request_back.edit', '\Modules\Admin\Policies\MachinePolicy@approveRequestBack');

        // Subscription
        Gate::define('adm.subscription.list', '\Modules\Admin\Policies\SubscriptionPolicy@list');
        Gate::define('adm.subscription.edit', '\Modules\Admin\Policies\SubscriptionPolicy@edit');
        Gate::define('adm.subscription.change', '\Modules\Admin\Policies\SubscriptionPolicy@change');
        Gate::define('adm.subscription.create', '\Modules\Admin\Policies\SubscriptionPolicy@create');
        Gate::define('adm.subscription.show', '\Modules\Admin\Policies\SubscriptionPolicy@show');
        Gate::define('adm.subscription_request.list', '\Modules\Admin\Policies\SubscriptionPolicy@request');
        Gate::define('adm.subscription_request.edit', '\Modules\Admin\Policies\SubscriptionPolicy@approve');
    }
}
