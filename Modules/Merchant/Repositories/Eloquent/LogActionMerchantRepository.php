<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\LogActionMerchant;
use Modules\Merchant\Repositories\LogActionMerchantRepositoryInterface;
use Yajra\DataTables\DataTables;

class LogActionMerchantRepository extends BaseRepository implements LogActionMerchantRepositoryInterface
{
    public function __construct(LogActionMerchant $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        $accountId = !empty($request->id) ? $request->id : auth(MERCHANT)->user()->id;
        $query = $this->model::query()
            ->where('account_id', $accountId)
            ->get();
        $actionName = config('merchant.route_action');
        return Datatables::of($query)
            ->addColumn('account_name', function ($row) {
                if(empty($row->merchant)){
                    return '';
                }
                $content = $row->merchant->name ?? $row->merchant->email;
                return $content;
            })
            ->addColumn('function', function ($row) use ($actionName) {
                $content = $actionName[$row->action]['function'] ?? '';
                return $content;
            })
            ->addColumn('action', function ($row) use ($actionName) {
                $content = $actionName[$row->action]['action'] ?? '';
                return $content;
            })
            ->addColumn('content_request', function ($row) {
                if(empty($row->content_request)){
                    return '';
                }
                try {
                    $content = [];
                    $contentRequest = json_decode($row->content_request);
                    foreach ($contentRequest as $key => $contents){
                        $content[] = $key . ': ' . $contents;
                    }
                    return !empty($content) ? implode('<br/>', $content) : '';
                } catch (\Exception $exception) {
                    return '';
                }
            })
            ->addColumn('created_at', function ($row) {
                return $content = '<span data-sort="' . strtotime($row->created_at) . '" data-search="' . date('d-m-Y H:i:s', strtotime($row->created_at)) . '">' . date('d/m/Y H:i:s',
                        strtotime($row->created_at)) . '</span>';;
            })
            ->rawColumns(['created_at', 'content_request'])
            ->make();
    }

    public function createAction($request, $attribute)
    {
        $data = $request->all();
        array_push($data, $request->route()->parameters());
        $attribute = [
            'account_id' => auth(MERCHANT)->id(),
            'action' => $request->route()->getName(),
            'parameter' => json_encode($data),
            'content_request' => json_encode($attribute['content_request']),
            'ip_address' => $request->ip(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->model->create($attribute);
    }
}
