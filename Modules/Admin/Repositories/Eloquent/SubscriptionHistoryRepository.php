<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\SubscriptionHistory;
use Modules\Admin\Repositories\SubscriptionHistoryRepositoryInterface;
use Yajra\DataTables\DataTables;

class SubscriptionHistoryRepository extends BaseRepository implements SubscriptionHistoryRepositoryInterface
{
    public function __construct(SubscriptionHistory $model)
    {
        parent::__construct($model);
    }

    public function list($request, $merchantId)
    {
        $history = $this->model::query()
            ->where('merchant_id', $merchantId)
            ->with(['merchant', 'machine'])
            ->get();

        return Datatables::of($history)
            ->addColumn('machine_name', function ($row) {
                if(empty($row->machine)){
                    return '';
                }
                $content = $row->machine->name;
                return $content;
            })
            ->addColumn('code', function ($row) {
                $content = $row->code;
                return $content;
            })
            ->addColumn('date_expiration_begin', function ($row) {
                $content = '<span data-sort="'.strtotime($row->date_expiration_begin).'" data-search="'.$row->date_expiration_begin->format('d-m-Y').'">'. $row->date_expiration_begin->format('d/m/Y') . '</span>';
                return $content;
            })
            ->addColumn('date_expiration_end', function ($row) {
                $content = '<span data-sort="'.strtotime($row->date_expiration_end).'" data-search="'.$row->date_expiration_end->format('d-m-Y').'">'. $row->date_expiration_end->format('d/m/Y') . '</span>';
                return $content;
            })
            ->addColumn('created_at', function ($row) {
                $content = '<span data-sort="'.strtotime($row->created_at).'" data-search="'.$row->created_at->format('d-m-Y').'">'. $row->created_at->format('d/m/Y') . '</span>';
                return $content;
            })
            ->addColumn('status', function ($row) {
                if($row->subscription_request_id == 0){
                    $content = '<span class="badge badge-warning">Thêm trực tiếp</span>';
                } else {
                    $content = '<span class="badge badge-info">Duyệt yêu cầu</span>';
                }
                return $content;
            })
            ->rawColumns(['date_expiration_begin', 'date_expiration_end', 'created_at', 'status'])
            ->make();
    }

    public function createSubscriptionHistory($arrAttributeSubscription, $dateExpireBegin, $dateExpireEnd)
    {
        $attributes = [
            'code' => !empty($arrAttributeSubscription['id']) ? $this->model->generateCode($arrAttributeSubscription['id']) : 'VTI-UPDATE',
            'merchant_id' => $arrAttributeSubscription['merchant_id'],
            'machine_id' => $arrAttributeSubscription['machine_id'],
            'subscription_request_id' => $arrAttributeSubscription['id'] ?? 0,
            'machine_position' => $arrAttributeSubscription['machine_address'] ?? '',
            'date_expiration_begin' => $dateExpireBegin,
            'date_expiration_end' => $dateExpireEnd . ' 23:59:59',
            'created_by' => auth(ADMIN)->user()->id,
            'status' => $this->model::IS_ACTIVE,
        ];
        $subscriptionHistory = $this->model->create($attributes);
        $subscriptionHistory->checksum = $subscriptionHistory->generateChecksum();
        $subscriptionHistory->save();
        return $subscriptionHistory;
    }
}
