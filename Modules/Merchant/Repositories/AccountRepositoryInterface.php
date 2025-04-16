<?php


namespace Modules\Merchant\Repositories;


interface AccountRepositoryInterface
{
    public function list();

    public function permission($request);

    public function findAccountByID($id);

    public function permissionChange($account, $request);

    public function destroy($merchant);

    public function updateProfile($merchant, $request);

    public function updateSetting($account, $merchant, $request);

    public function updateAccount($account, $request);

    public function storeAccount($request);

    public function findChildrenAccounts();
}
