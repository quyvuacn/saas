<?php

// Merchant /////////////////

use Illuminate\Support\Facades\Artisan;
Route::domain(env('DOMAIN_REGISTER'))->group(function(){
    Route::get('/', 'Auth\RegisterController@showRegistrationForm')->name('merchant.register');
    Route::get('/register-success', 'Auth\RegisterController@registerSuccess')->name('merchant.register.success');
    Route::post('/register', 'Auth\RegisterController@register')->name('merchant.register');
});

Route::domain(env('DOMAIN_CMS'))->group(function () {
    // LOGIN
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('merchant.login');
    Route::post('/login', 'Auth\LoginController@login')->name('merchant.login');
    Route::post('/logout', 'Auth\LoginController@logout')->name('merchant.logout');

    // PASS FORGOT
    Route::get('/forgot-password', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('merchant.password.forgot');
    Route::post('/email-password', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('merchant.password.email');

    // PASS RESET
    Route::get('/reset-password/{token}', 'Auth\ResetPasswordController@showResetForm')->name('merchant.password.reset');
    Route::post('/reset-password', 'Auth\ResetPasswordController@update')->name('merchant.password.update');

    // PASS CONFIRM ??
    Route::get('/confirm-password', 'Auth\ConfirmPasswordController@showConfirmForm')->name('merchant.password.confirm');
    Route::post('/confirm-password', 'Auth\ConfirmPasswordController@confirm')->name('merchant.password.confirm');

    // DASHBOARD
    Route::get('/', 'MerchantController@dashboard')->name('merchant.dashboard');
    // CHART
    Route::post('/chart-dashboard-revenue', 'MerchantController@getChartRevenue')->name('merchant.dashboard.revenue');

    // ACOUNT
    Route::prefix('account')->group(function () {
        Route::get('/', 'AccountController@list')->name('merchant.account.list');
        Route::get('/history', 'AccountController@history')->name('merchant.account.history');
        Route::get('/history/{id}', 'AccountController@history')->name('merchant.account.historyMerchant');
        Route::get('/profile', 'AccountController@profile')->name('merchant.account.profile');
        Route::post('/profile', 'AccountController@updateProfile')->name('merchant.account.profile');
    });

    // ACOUNT
    Route::prefix('account')->middleware('check.has.approved')->group(function () {
        Route::get('/', 'AccountController@list')->name('merchant.account.list');
        Route::get('/setting', 'AccountController@setting')->name('merchant.account.setting');
        Route::post('/setting', 'AccountController@updateSetting')->name('merchant.account.setting');
        Route::get('/create', 'AccountController@create')->name('merchant.account.create')->middleware('check.extra.account:merchant');
        Route::post('/create', 'AccountController@store')->name('merchant.account.create')->middleware('check.extra.account:merchant');
        Route::get('/edit/{account}', 'AccountController@edit')->name('merchant.account.edit');
        Route::post('/edit/{account}', 'AccountController@update')->name('merchant.account.edit');
        Route::post('/delete/{merchant}', 'AccountController@destroy')->name('merchant.account.delete');
        Route::get('/permission', 'AccountController@permission')->name('merchant.account.permission');
        Route::post('/permission/change/{account}', 'AccountController@permissionChange')->name('merchant.account.permissionChange');
        Route::post('/permission', 'AccountController@permission')->name('merchant.account.permissionAjax');
    });

    // MẠCHINE
    Route::prefix('machine')->middleware('check.has.approved')->group(function () {
        Route::get('/', 'MachineController@list')->name('merchant.machine.list')->middleware('check.has.machine');
        Route::get('/history', 'MachineController@history')->name('merchant.machine.history')->middleware('check.has.machine');
        Route::get('/history-export', 'MachineController@historiesExport')->name('merchant.machine.historiesExport')->middleware('check.has.machine');
        Route::post('/history-ajax', 'MachineController@history')->name('merchant.machine.historyAjax')->middleware('check.has.machine');

        Route::get('/request', 'MachineController@request')->name('merchant.machine.request');
        Route::get('/request/{request}/edit', 'MachineController@editRequest')->name('merchant.machine.editRequest');
        Route::post('/request/{request}/edit', 'MachineController@updateRequest')->name('merchant.machine.updateRequest');
        Route::get('/request-list', 'MachineController@listRequest')->name('merchant.machine.listRequest');
        Route::get('/request-history', 'MachineController@requestHistory')->name('merchant.machine.requestHistory');
        Route::post('/request', 'MachineController@requestMachine')->name('merchant.machine.request');
        Route::post('/request/{request}/delete', 'MachineController@deleteRequest')->name('merchant.machine.deleteRequest');

        Route::post('/change-address/{machine}', 'MachineController@changeAddress')->name('merchant.machine.changeAddress')->middleware('check.has.machine');
        Route::post('/{machine}/request-back', 'MachineController@requestBack')->name('merchant.machine.requestBack')->middleware('check.has.machine');
    });

    // SUBSCRIPTION
    Route::prefix('subscription')->middleware('check.has.approved')->group(function () {
        Route::get('/', 'SubscriptionController@list')->name('merchant.subscription.list')->middleware('check.has.machine');
        Route::get('/history', 'SubscriptionController@history')->name('merchant.subscription.history')->middleware('check.has.machine');
        Route::post('/history-ajax', 'SubscriptionController@history')->name('merchant.subscription.historyAjax')->middleware('check.has.machine');
        Route::post('/extend', 'SubscriptionController@extend')->name('merchant.subscription.extend')->middleware('check.has.machine');

    });

    // PRODUCT
    Route::prefix('product')->middleware('check.has.approved')->group(function () {
        Route::get('/', 'ProductController@list')->name('merchant.product.list')->middleware('check.has.machine');
        Route::get('/selling', 'ProductController@selling')->name('merchant.product.selling')->middleware('check.has.machine');
        Route::get('/create', 'ProductController@create')->name('merchant.product.create')->middleware('check.has.machine');
        Route::get('/edit/{product}', 'ProductController@edit')->name('merchant.product.edit')->middleware('check.has.machine');
        Route::post('/update/{product}', 'ProductController@update')->name('merchant.product.update')->middleware('check.has.machine');
        Route::post('/create', 'ProductController@store')->name('merchant.product.create')->middleware('check.has.machine');
        Route::put('/delete/{product}', 'ProductController@destroy')->name('merchant.product.delete')->middleware('check.has.machine');
        Route::get('/sync', 'ProductController@sync')->name('merchant.product.sync')->middleware('check.has.machine');
        Route::get('/sync-detail/{machine}', 'ProductController@syncDetail')->name('merchant.product.syncDetail')->middleware('check.has.machine');
        Route::post('/{pack}/toggle-pack', 'ProductController@togglePack')->name('merchant.product.togglePack')->middleware('check.has.machine');
        Route::post('/{machine}/update-machine-product', 'ProductController@updateMachineProducts')->name('merchant.product.updateMachineProducts')->middleware('check.has.machine');
        Route::post('/upload', 'ProductController@upload')->name('merchant.product.upload');
    });

    // USER
    Route::prefix('user')->middleware('check.has.approved')->group(function () {
        Route::get('/', 'UserController@list')->name('merchant.user.list');
        Route::get('/create', 'UserController@create')->name('merchant.user.create');
        Route::post('/create', 'UserController@store')->name('merchant.user.create');
        Route::post('/import', 'UserController@import')->name('merchant.user.import');
        Route::get('/user-export', 'UserController@exportUser')->name('merchant.user.exportUser');
        Route::get('/credit', 'UserController@credit')->name('merchant.user.credit');
        Route::get('/debt', 'UserController@debt')->name('merchant.user.debt');
        Route::post('/{debtUser}/debt-received', 'UserController@debtReceived')->name('merchant.user.debtReceived');
        Route::post('/debt-collection-active', 'UserController@debtCollectionActivation')->name('merchant.user.debtCollectionActivation');
        Route::post('/debt-collection-disable', 'UserController@debtCollectionDisable')->name('merchant.user.debtCollectionDisable');
        Route::get('/recharge', 'UserController@recharge')->name('merchant.user.recharge');
        Route::get('/approve/{approve}', 'UserController@approve')->name('merchant.user.approve');
        Route::post('/approve/{approve}', 'UserController@approveCredit')->name('merchant.user.approve');
        Route::get('/recharge-search', 'UserController@rechargeSearch')->name('merchant.user.rechargeSearch');
        Route::get('/approve-option/{approve}', 'UserController@approveOption')->name('merchant.user.approveOption');
        Route::post('/approve-option/{approve}', 'UserController@approveOptionStore')->name('merchant.user.approveOption');
        // Route::post('/approve-all', 'UserController@approveAllRequest')->name('merchant.user.approveAllRequest');
        Route::post('/delete/{user}', 'UserController@destroy')->name('merchant.user.delete');
        Route::post('/credit-delete/{user}', 'UserController@destroyCredit')->name('merchant.user.deleteCredit');
        Route::post('/recharge-delete/{coin}', 'UserController@destroyCoinRequest')->name('merchant.user.deleteCoinRequest');
        Route::post('/recharge-quick-approve/{coin}', 'UserController@quickApprove')->name('merchant.user.quickApprove');
        Route::get('/recharge-create', 'UserController@rechargeCreate')->name('merchant.user.rechargeCreate');
        Route::post('/recharge-store', 'UserController@rechargeStore')->name('merchant.user.rechargeStore');
        Route::get('/recharge-export', 'UserController@rechargeExport')->name('merchant.user.rechargeExport');
        Route::post('/search', 'UserController@userSearch')->name('merchant.user.userSearch');
        Route::get('/debt-export', 'UserController@export')->name('merchant.user.exportDebt');
        if (app()->isLocal()) {
            Route::get('/debt-cron-test', function () {
                Artisan::call('report:user_debt');
            });
            Route::get('/coin-request-cron-test', function () {
                Artisan::call('coin_request:clear');
            });
        }
    });

    // STAFF
    Route::prefix('staff')->middleware('check.has.approved')->group(function () {
        Route::get('/', 'MerchantSettingStaffController@list')->name('merchant.staff.list');
        Route::post('/{staff}/delete', 'MerchantSettingStaffController@destroy')->name('merchant.staff.delete');
        Route::post('/bulk-delete', 'MerchantSettingStaffController@bulkDelete')->name('merchant.staff.bulkDelete');
        Route::post('/{staff}/edit', 'MerchantSettingStaffController@editStaff')->name('merchant.staff.edit');
        Route::get('/staff-export', 'MerchantSettingStaffController@export')->name('merchant.staff.export');
        Route::post('/staff-import', 'MerchantSettingStaffController@import')->name('merchant.staff.import');
    });

    // ADS
    Route::prefix('ads')->middleware('check.has.approved')->group(function () {
        Route::get('/', 'MerchantAdsController@list')->name('merchant.ads.list')->middleware('check.has.machine');;
        Route::get('/create', 'MerchantAdsController@create')->name('merchant.ads.create')->middleware('check.has.machine');;
        Route::get('/{ads}/edit', 'MerchantAdsController@edit')->name('merchant.ads.edit')->middleware('check.has.machine');;
        Route::post('/{ads}/update', 'MerchantAdsController@update')->name('merchant.ads.update')->middleware('check.has.machine');;
        Route::post('/create', 'MerchantAdsController@store')->name('merchant.ads.create')->middleware('check.has.machine');;
        Route::put('/{ads}/delete', 'MerchantAdsController@destroy')->name('merchant.ads.delete')->middleware('check.has.machine');;
        Route::post('/upload', 'MerchantAdsController@upload')->name('merchant.ads.upload')->middleware('check.has.machine');;
    });


    Route::prefix('notify')->middleware('check.has.approved')->group(function () {
        Route::get('/', 'MerchantNotifyController@list')->name('merchant.notify.list');
        Route::get('/create', 'MerchantNotifyController@create')->name('merchant.notify.create');
        Route::post('/create', 'MerchantNotifyController@store')->name('merchant.notify.store');
        Route::get('/edit/{notifyId}', 'MerchantNotifyController@edit')->name('merchant.notify.edit');
        Route::post('/edit/{notifyId}', 'MerchantNotifyController@update')->name('merchant.notify.update');
        Route::put('/delete/{notifyId}', 'MerchantNotifyController@destroy')->name('merchant.notify.delete');
    });
});
