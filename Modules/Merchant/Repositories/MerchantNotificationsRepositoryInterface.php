<?php


namespace Modules\Merchant\Repositories;


interface MerchantNotificationsRepositoryInterface
{
    public function list($request);

    public function storeNotify($request);

    public function findById($notifyId);

    public function destroy($notify);

    public function updateNotify($notify, $request);

    public function isPushNotify();
}
