<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\MerchantNotifications;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Repositories\MerchantNotificationsRepositoryInterface;
use Yajra\DataTables\DataTables;

class MerchantNotificationsRepository extends BaseRepository implements MerchantNotificationsRepositoryInterface
{
    public function __construct(MerchantNotifications $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        $merchant = auth(MERCHANT)->user();
        $query = $this->model::query()
            ->where('merchant_id', $merchant->getMerchantID());
        $datatables = Datatables::of($query)
            ->addColumn('id', function ($row) {
                return $row->id;
            })->addColumn('title', function ($row) {
                return $row->title;
            })->addColumn('created_at', function ($row) {
                return sortSearchDate($row->created_at);
            })->addColumn('time_begin_show', function ($row) {
                return sortSearchDate($row->time_begin_show);
            })->addColumn('time_end_show', function ($row) {
                return sortSearchDate($row->time_end_show);
            });

        if ($merchant->can('notify.edit')) {
            $datatables->addColumn('edit', function($row){
                $action = '<a href="'.route('merchant.notify.edit', ['notifyId' => $row->id]).'" class="btn btn-primary">Sửa</a>';
                return $action;
            });
            $datatables->addColumn('delete', function($row){
                $action = '<button onclick="deleteNotify('.$row->id.')" class="btn btn-danger">Xóa</button>';
                return $action;
            });
        } else {
            $datatables->addColumn('edit', function($row){
                return '';
            });
            $datatables->addColumn('delete', function($row){
                return '';
            });
        }
        return $datatables->rawColumns(['created_at', 'time_begin_show' , 'time_end_show', 'edit', 'delete'])
            ->make(true);
    }

    public function storeNotify($request)
    {
        try {
            $account = auth(MERCHANT)->user();

            $date      = date('yy/m/d', time());
            $imageName = '';
            if (!empty($request->file)) {
                $imageName = time() . '-' . $request->file->getClientOriginalName();
                $request->file->move(env('DIRECTORY_STORAGE') . 'images/notify/' . $date, $imageName);
            }

            $model = $this->model::create([
                'title' => $request->name,
                'time_begin_show' => convertDateTimeFlatpickr($request->time_begin_show),
                'time_end_show' => convertDateTimeFlatpickr($request->time_end_show),
                'image' => 'images/notify/' . $date . '/' . $imageName,
                'brief' => $request->brief,
                'content' => $request->content,
                'created_by' => $account->id,
                'status' => $this->model::UNREADED,
                'merchant_id' => $account->getMerchantID(),
            ]);
            $this->setData($model->id);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Tạo mới thông báo thành công!');
        } catch (\Exception $e) {
            Log::error('[MerchantNotifyRepository][createNotify]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Tạo mới thông báo không thành công!');
        }
        return $this->response;
    }



    public function findById($notifyId)
    {
        $model = $this->model::query()->where('id', $notifyId)->where('is_deleted', '<>', $this->model::IS_DELETED)->first();
        if(!$model)
            return $model;
        return $model->merchant_id != auth(MERCHANT)->user()->getMerchantID() ? false : $model;
    }

    public function destroy($notify)
    {
        try {
            $account = auth(MERCHANT)->user();

            $notify->updated_by = $account->id;
            $notify->is_deleted = $this->model::IS_DELETED;
            $notify->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Xoá thông báo thành công!');
        } catch (\Exception $e) {
            Log::error('[MerchantNotifyRepository][deleteNotify]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Xoá thông báo không thành công!');
        }
        return $this->response;
    }

    public function updateNotify($notify, $request){
        try {
            $account = auth(MERCHANT)->user();

            $date      = date('yy/m/d', time());
            if (!empty($request->file)) {
                $imageName = time() . '-' . $request->file->getClientOriginalName();
                $request->file->move(env('DIRECTORY_STORAGE') . 'images/notify/' . $date, $imageName);
                $notify->image = 'images/notify/' . $date . '/' . $imageName;
            }
            $notify->title = $request->name;
            $notify->time_begin_show = convertDateTimeFlatpickr($request->time_begin_show);
            $notify->time_end_show = convertDateTimeFlatpickr($request->time_end_show);
            $notify->brief = $request->brief;
            $notify->content = $request->content;
            $notify->updated_by = $account->id;
            $notify->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Sửa thông báo thành công!');
        } catch (\Exception $e) {
            Log::error('[MerchantNotifyRepository][editNotify]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Sửa thông báo không thành công!');
        }
        return $this->response;
    }

    public function isPushNotify()
    {
        $dateCurrent = date('Y-m-d H:i:s');
        $result = $this->model::query()
            ->where('time_begin_show', '<=', $dateCurrent)
            ->where('time_end_show', '>=', $dateCurrent)
            ->where('status', $this->model::UNREADED)
            ->get()
            ->all();
        return $result;
    }
}
