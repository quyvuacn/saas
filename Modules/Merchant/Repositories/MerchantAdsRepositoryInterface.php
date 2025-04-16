<?php


namespace Modules\Merchant\Repositories;


interface MerchantAdsRepositoryInterface
{
    public function list();

    public function findAdsByID($id);

    public function destroy($ads);

    public function updateAds($ads, $request);

    public function storeAds($request);
}
