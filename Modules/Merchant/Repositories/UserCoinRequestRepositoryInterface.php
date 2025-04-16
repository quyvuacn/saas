<?php


namespace Modules\Merchant\Repositories;


use App\Models\UserCoinRequest;

interface UserCoinRequestRepositoryInterface
{
    public function findCoinRequestByID($id);

    public function findAllCoinRequests();

    public function quickApprove($coin);

    public function destroyCoinRequest($coin);

    public function approveOptionStore($coin, $request);

    public function getUnApproveCoinRequests();

    public function clearUnApproveCoinRequests($coinRequests);

}
