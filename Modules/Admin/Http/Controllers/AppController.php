<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Classes\Facades\AdminCan;
use Modules\Admin\Repositories\AppVersionRepositoryInterface;
use Modules\Admin\Http\Requests\AppRequest;
use Modules\Admin\Repositories\LogActionAdminRepositoryInterface;

class AppController extends Controller
{
    protected $appVersionRepository;

    protected $logActionAdminRepository;

    public function __construct(
        AppVersionRepositoryInterface $appVersionRepository,
        LogActionAdminRepositoryInterface $logActionAdminRepository
    )
    {
        $this->appVersionRepository = $appVersionRepository;
        $this->logActionAdminRepository = $logActionAdminRepository;
    }

    public function list(Request $request)
    {
        if (!AdminCan::do('adm.machine.app_version_list')) {
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        if ($request->ajax()) {
            return $this->appVersionRepository->list($request);
        }

        return view('admin::app.list');
    }

    public function create()
    {
        if (!AdminCan::do('adm.machine.app_version_edit')) {
            return redirect()->route('admin.app.list')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        return view('admin::app.create');
    }

    public function store(AppRequest $request)
    {
        if (!AdminCan::do('adm.machine.app_version_edit')) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn này!')];
        }
        $response = $this->appVersionRepository->store($request);

        if($response['status']){
            $data = $response['data'];
            $attribute['content_request'] = [
                'ID' => $data->id,
                'version' => $data->version
            ];
            $this->logActionAdminRepository->createAction($request, $attribute);
        }
        return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
    }
}
