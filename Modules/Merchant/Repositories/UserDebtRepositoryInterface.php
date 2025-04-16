<?php


namespace Modules\Merchant\Repositories;


interface UserDebtRepositoryInterface
{
    public function list($request);

    public function findUserDebts();

    public function createDebtReport($user);

    public function findDebtUserByID($debtUser);

    public function findDebByUserID($userId);

    public function receivedDebt($debtUser);

    public function debtCollectionActivation();

    public function debtCollectionDisable();

    public function decreaseUserDebt($debtUser, $coin);
}
