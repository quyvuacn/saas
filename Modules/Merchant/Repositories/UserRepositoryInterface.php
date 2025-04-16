<?php


namespace Modules\Merchant\Repositories;


interface UserRepositoryInterface
{
    public function list($request);

    public function credit($request);

    public function store($request);

    public function destroy($user);

    public function rechargeSearch($request);

    public function findUserToApprove($id, $merchant_id);

    public function approveCredit($approve, $userRequest);

    public function findUserByID($user_id);

    public function userSearch($email);

    public function rechargeStore($request);

    public function getTotalNewUsers();

    public function getDebtUsers();

    public function getUserByMerchantId($merchantId);
}
