<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\MerchantAds;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Repositories\MerchantAdsRepositoryInterface;
use Yajra\DataTables\DataTables;

class MerchantAdsRepository extends BaseRepository implements MerchantAdsRepositoryInterface {

    public function __construct(MerchantAds $model)
    {
        parent::__construct($model);
    }

    public function list()
    {
        $adss = $this->getAdssOfMerchant()->get();
        $datatables = DataTables::of($adss)
            ->addColumn('image', function ($row) {
                $url = '<div class="text-center"><a href="' . route("merchant.ads.edit", ["ads" => $row->id]) . '"><img width="200px" height="80px" style="object-fit: cover; width: 100%; border-radius: 5px" src="' . makeImageAds($row->image) . '"/></a></div>';
                return $url;
            })
            ->addColumn('machine_model', function ($row) {
                if ($row->machines->count()) {
                    $content = '<ul class="list-group list-group-flush p-0 m-0 text-center">';
                    foreach ($row->machines as $machine) {
                        $content .= '<li class="list-group-item pt-1 pb-1">' . ($machine->name !== '' ? $machine->name : '---') . ' / ' . ($machine->model !== '' ? $machine->model : '---') . '</li>';
                    }
                    $content .= '</ul>';
                    return $content;
                } else {
                    return '---';
                }
            })
            ->addColumn('machine_address', function ($row) {
                if ($row->machines->count()) {
                    $content = '<ul class="list-group list-group-flush p-0 m-0 text-center">';
                    foreach ($row->machines as $machine) {
                        $content .= '<li class="list-group-item pt-1 pb-1">' . ($machine->machine_address !== '' && $machine->machine_address ? $machine->machine_address : '---') . '</li>';
                    }
                    $content .= '</ul>';
                    return $content;
                } else {
                    return '---';
                }
            })
            ->addColumn('start_date', function ($row) {
                return sortSearchDate($row->start_date);
            })
            ->addColumn('end_date', function ($row) {
                return sortSearchDate($row->end_date);
            });
        if (MerchantCan::do('ads.edit')) {
            $datatables->addColumn('action', function ($row) {
                return '<a href="' . route("merchant.ads.edit", ["ads" => $row->id]) . '" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Sửa</a>
<a onclick="deleteAds(' . $row->id . ');return false;" href="javascript:;" class="btn btn-danger btn-sm btn-delete-ads"><i class="fas fa-trash"></i> Xóa</a>';
            });
        }
        return $datatables->rawColumns(['image', 'action', 'start_date', 'end_date', 'machine_address', 'machine_model'])
            ->make(true);

    }

    public function getAdssOfMerchant()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()->with('machines')
            ->where('merchant_id', $merchant->getMerchantID())
            ->where('is_deleted', '<>', $this->model::IS_DELETED)
            ->orderBy('created_at', 'DESC');
    }

    public function findAdsByID($id)
    {
        return $this->model::query()->where('id', $id)->where('is_deleted', '<>', $this->model::IS_DELETED)->first();
    }

    public function destroy($ads)
    {
        try {
            $ads->is_deleted = $this->model::IS_DELETED;
            $ads->updated_by = auth(MERCHANT)->id();
            $ads->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Xóa Quảng cáo thành công!'));
        } catch (\Exception $e) {
            Log::error('[MerchantAdsRepository][destroy]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Xóa Quảng cáo không thành công!'));
        }
        return $this->response;
    }

    public function updateAds($ads, $request)
    {
        try {
            $ads->start_date = convertDateFlatpickr($request->start_date);
            $ads->end_date   = convertDateFlatpickr($request->end_date);
            $ads->updated_by = auth(MERCHANT)->id();
            if (!empty($request->file)) {
                $date      = date('yy/d/m', time());
                $imageName = time() . '-' . $request->file->getClientOriginalName();
                $request->file->move(env('DIRECTORY_STORAGE') . 'images/ads/' . $date, $imageName);
                $image      = 'images/ads/' . $date . '/' . $imageName;
                $ads->image = $image;
            }
            $ads->save();
            if ($ads) {
                $ads->machines()->detach();
                $ads->machines()->attach($request->machines_list);
            }
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Cập nhật Quảng cáo thành công!'));
        } catch (\Exception $e) {
            Log::error('[MerchantAdsRepository][updateAds]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Cập nhật Quảng cáo không thành công!'));
        }
        return $this->response;
    }

    public function storeAds($request)
    {
        try {
            $account   = auth(MERCHANT)->user();
            $date      = date('yy/d/m', time());
            $imageName = '';
            if (!empty($request->file)) {
                $imageName = time() . '-' . $request->file->getClientOriginalName();
                $request->file->move(env('DIRECTORY_STORAGE') . 'images/ads/' . $date, $imageName);
            }
            $ads = $this->model::create([
                'start_date'  => Carbon::createFromFormat('d/m/Y', $request->start_date)->toDateTime(),
                'end_date'    => Carbon::createFromFormat('d/m/Y', $request->end_date)->toDateTime(),
                'merchant_id' => $account->getMerchantID(),
                'created_by'  => $account->id,
                'image'       => $imageName ? 'images/ads/' . $date . '/' . $imageName : public_path('/images/noimage.jpg'),
            ]);
            if ($ads) {
                $ads->machines()->attach($request->machines_list);
            }
            $this->setData($ads->id);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Tạo Quảng cáo thành công!'));
        } catch (\Exception $e) {
            Log::error('[MerchantAdsRepository][storeAds]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Tạo Quảng cáo không thành công!'));
        }
        return $this->response;
    }
}
