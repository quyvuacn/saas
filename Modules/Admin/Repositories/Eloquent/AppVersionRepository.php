<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Admin;
use App\Models\AccountPermission;
use App\Models\AppVersion;
use App\Models\Permission;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Repositories\AdminRepositoryInterface;
use datatables;
use Modules\Admin\Repositories\AppVersionRepositoryInterface;

class AppVersionRepository extends BaseRepository implements AppVersionRepositoryInterface
{

    public function __construct(AppVersion $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        $model = $this->model::query()->get();

        return \Yajra\DataTables\DataTables::of($model)
            ->addColumn('stt', function ($row) {
                return $row->id;
            })
            ->addColumn('version', function ($row) {
                return $row->version;
            })
            ->addColumn('version_code', function ($row) {
                return $row->code;
            })
            ->addColumn('url', function ($row) {
                return '<a href="'.getLinkAppVersion($row->link).'" target="_blank">'.getLinkAppVersion($row->link).'</a>';
            })
            ->addColumn('brief', function ($row) {
                return $row->brief;
            })
            ->addColumn('created_at', function ($row) {
                return '<span class="d-none">'.strtotime($row->created_at).'_'.date('d/m/Y', strtotime($row->created_at)).'</span><span>'.date('d/m/Y', strtotime($row->created_at)).'</span>';
            })
            ->rawColumns(['created_at', 'url'])
            ->make(true);
    }


    public function store($request)
    {
        try {
            $account = auth(\ADMIN)->user();
            $date      = date('yy/d/m', time());
            $fileAPK = '';
            if (!empty($request->file)) {
                $fileAPK = time() . '-' . $request->file->getClientOriginalName();
                $request->file->move(env('DIRECTORY_STORAGE') . 'app/' . $date, $fileAPK);
            }
            $model = $this->model::create([
                'version'          => $request->version,
                'code'             => $request->code,
                'brief'            => $request->brief,
                'created_by'       => $account->id,
                'link'             => 'app/' . $date . '/' . $fileAPK
            ]);
            $this->setData($model);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Tạo phiên bản mới thành công!'));
        } catch (\Exception $e) {
            Log::error('[Adm][AppVersion][store]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Tạo phiên bản mới không thành công!'));
        }
        return $this->response;
    }
}
