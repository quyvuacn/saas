<?php
// AdminVTI /////////////////

Route::domain(env('DOMAIN_ADMIN'))->group(function () {

    // LOGIN
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\LoginController@login')->name('admin.login');
    Route::post('/logout', 'Auth\LoginController@logout')->name('admin.logout');

    // PASS FORGOT
    Route::get('/forgot-password', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('admin.password.forgot');
    Route::post('/email-password', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');

    // PASS RESET
    Route::get('/reset-password/{token}', 'Auth\ResetPasswordController@showResetForm')->name('admin.password.reset');
    Route::post('/reset-password', 'Auth\ResetPasswordController@update')->name('admin.password.update');

    // PASS CONFIRM ??
    Route::get('/confirm-password', 'Auth\ConfirmPasswordController@showConfirmForm')->name('admin.password.confirm');
    Route::post('/confirm-password', 'Auth\ConfirmPasswordController@confirm')->name('admin.password.confirm');

    Route::get('/', 'AdminController@dashboard')->name('admin.dashboard');
    Route::post('get-data-chart-machine', 'AdminController@getDataChartMachine')->name('admin.getDataChartMachine');
    // Account Management
    Route::prefix('account')->group(function () {
        Route::get('/', 'AccountController@list')->name('admin.account.list');
        Route::get('/create', 'AccountController@create')->name('admin.account.create');
        Route::post('/create', 'AccountController@store')->name('admin.account.create');
        Route::get('/{account}/edit/', 'AccountController@edit')->name('admin.account.edit');
        Route::post('/{account}/edit/', 'AccountController@update')->name('admin.account.edit');
        Route::get('/profile', 'AccountController@profile')->name('admin.account.profile');
        Route::post('/profile', 'AccountController@updateProfile')->name('admin.account.profile');
        Route::get('/permission', 'AccountController@permission')->name('admin.account.permission');
        Route::post('/permission', 'AccountController@permission')->name('admin.account.permissionAjax');
        Route::post('/permission/change/{account}', 'AccountController@permissionChange')->name('admin.account.permissionChange');
        Route::post('/{account}/delete', 'AccountController@destroy')->name('admin.account.delete');
        Route::post('/{account}/toggle', 'AccountController@toggleStatus')->name('admin.account.toggle');
        Route::get('/history', 'AccountController@history')->name('admin.account.history');
        Route::get('/history/{id}', 'AccountController@history')->name('admin.account.historyAdmin');
    });
    // Machine Management
    Route::prefix('machine')->group(function () {
        Route::get('/', 'MachineController@list')->name('admin.machine.list');
        Route::get('/create', 'MachineController@create')->name('admin.machine.create');
        Route::post('/create-post', 'MachineController@createPost')->name('admin.machine.createPost');
        Route::get('/edit/{machine}', 'MachineController@edit')->name('admin.machine.edit');
        Route::post('/update/{machine}', 'MachineController@update')->name('admin.machine.update');
        Route::post('/delete/{machine}', 'MachineController@delete')->name('admin.machine.delete');
        Route::get('/request', 'MachineController@request')->name('admin.machine.request');
        Route::get('/request/{merchantRequest}', 'MachineController@requestDetail')->name('admin.machine.requestDetail');
        Route::post('/approve-request/{merchantRequest}', 'MachineController@approveRequest')->name('admin.machine.approveRequest');
        Route::post('/final-approve-request/{merchantRequest}', 'MachineController@finalApproveRequest')->name('admin.machine.finalApproveRequest');
        Route::post('/final-request-processing/{merchantRequest}', 'MachineController@finalRequestProcessing')->name('admin.machine.finalRequestProcessing');
        Route::get('/request-back', 'MachineController@requestBack')->name('admin.machine.requestBack');
        Route::get('/request-back/{machineRequest}', 'MachineController@requestBackDetail')->name('admin.machine.requestBackDetail');
        Route::post('/approve-request-back/{machineRequest}', 'MachineController@approveRequestBack')->name('admin.machine.approveRequestBack');
        Route::post('/final-approve-request-back/{machineRequest}', 'MachineController@finalApproveRequestBack')->name('admin.machine.finalApproveRequestBack');
        Route::post('/final-request-back-processing/{machineRequest}', 'MachineController@finalRequestBackProcessing')->name('admin.machine.finalRequestBackProcessing');
        Route::post('/cancel-request-back/{machineRequest}', 'MachineController@cancelRequestBack')->name('admin.machine.cancelRequestBack');
        Route::get('/request-processing', 'MachineController@requestProcessing')->name('admin.machine.requestProcessing');
        Route::get('/create-attributes', 'MachineController@createAttributes')->name('admin.machine.createAttributes');
        Route::post('/create-attributes-post', 'MachineController@createAttributesPost')->name('admin.machine.createAttributesPost');
        Route::post('/get-attributes', 'MachineController@getAttributes')->name('admin.machine.getAttributes');
        Route::post('/change-device/{machineId}', 'MachineController@changeDevice')->name('admin.machine.changeDevice');
    });
    // Merchant Management
    Route::prefix('merchant')->group(function () {
        Route::get('/', 'MerchantController@list')->name('admin.merchant.list');
        Route::get('/edit/{merchantId}', 'MerchantController@edit')->name('admin.merchant.edit');
        Route::post('/store/{merchantId}', 'MerchantController@store')->name('admin.merchant.store');
        Route::get('/request', 'MerchantController@request')->name('admin.merchant.request');
        Route::get('/request/{merchantRequest}', 'MerchantController@requestDetail')->name('admin.merchant.requestDetail');
        Route::put('/delete/{merchant}', 'MerchantController@delete')->name('admin.merchant.delete');
        Route::post('/approve-request/{merchantRequest}', 'MerchantController@approveRequest')->name('admin.merchant.approveRequest');
        Route::post('/final-approve-request/{merchantRequest}', 'MerchantController@finalApproveRequest')->name('admin.merchant.finalApproveRequest');
    });
    // Subscription Management
    Route::prefix('subscription')->group(function () {
        Route::get('/', 'SubscriptionController@list')->name('admin.subscription.list');
        Route::get('/extend', 'SubscriptionController@extend')->name('admin.subscription.extend');
        Route::get('/create', 'SubscriptionController@create')->name('admin.subscription.create');
        Route::post('/store', 'SubscriptionController@store')->name('admin.subscription.store');
        Route::get('/edit/{subscription}', 'SubscriptionController@edit')->name('admin.subscription.edit');
        Route::post('/update/{subscription}', 'SubscriptionController@update')->name('admin.subscription.update');
        Route::get('/approve/{subscriptionRequest}', 'SubscriptionController@approve')->name('admin.subscription.approve');
        Route::get('/viewRequest/{subscriptionRequest}', 'SubscriptionController@viewRequest')->name('admin.subscription.viewRequest');
        Route::get('/history/{merchantId}', 'SubscriptionController@history')->name('admin.subscription.history');
        Route::post('/approveRequest/{subscriptionRequest}', 'SubscriptionController@approveRequest')->name('admin.subscription.approveRequest');
        Route::post('/finalApproveRequest/{subscriptionRequest}', 'SubscriptionController@finalApproveRequest')->name('admin.subscription.finalApproveRequest');
    });
    // Subscription Management
    Route::prefix('app')->group(function () {
        Route::get('/', 'AppController@list')->name('admin.app.list');
        Route::get('/create', 'AppController@create')->name('admin.app.create');
        Route::post('/store', 'AppController@store')->name('admin.app.store');
    });
});
