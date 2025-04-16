<?php

namespace Modules\Merchant\Repositories;

interface MerchantSettingStaffInterface
{
    public function list($request);

    public function findStaffByID($staff_id);

    public function findStaffsByIDs($staff_ids);

    public function editStaff($staff, $request);

    public function destroy($staff);

    public function bulkDelete($staff_ids);

    public function findAll();
}
