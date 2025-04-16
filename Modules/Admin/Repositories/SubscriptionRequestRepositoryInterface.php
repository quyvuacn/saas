<?php

namespace Modules\Admin\Repositories;

interface SubscriptionRequestRepositoryInterface
{
    public function list($request);

    public function createSubscriptionRequest($request);

    public function findSubscriptionApprove($subscriptionRequest);

    public function findSubscriptionFinalApprove($subscriptionRequest);

    public function finalUpdateStatusRequest($subscriptionRequest, $status);

    public function updateStatusRequestCancel($subscriptionRequest, $request);

    public function approveSubscriptionRequest($subscriptionRequest, $request);

    public function getStatusRequest();

    public function getStatusRequestSuccess();

    public function getStatusRequestCancel();

    public function getStatusRequestNew();

    public function getTotalRequest();
}
