<?php


namespace Modules\Merchant\Repositories;


interface ProductRepositoryInterface
{
    public function list();

    public function selling();

    public function findProductByID($id);

    public function destroy($product);

    public function updateProduct($product, $request);

    public function storeProduct($request);

    public function getTotalProducts();
}
