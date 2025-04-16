<?php

namespace Modules\Admin\Repositories;

interface MachineRequestBackRepositoryInterface
{
    public function list($request);

    public function listProcessing();

    public function approveRequestBack($machineRequest, $request);

    public function finalApproveRequestBack($machineRequest);

    public function finalRequestBackProcessing($machineRequest);

    public function cancelRequestBack($machineRequest);

    public function listNewRequestDashboard();

    public function getTotalNewRequest();
}
