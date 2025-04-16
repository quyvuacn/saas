<?php

namespace Modules\Admin\Repositories\Eloquent;

use Illuminate\Http\Response;
use Modules\Admin\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements EloquentRepositoryInterface
{
    protected $model;
    protected $response;

    const IS_DELETED = 1;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->response['message'] = null;
        $this->response['status'] = true;
        $this->response['data'] = null;
        $this->response['alert'] = null;
        $this->response['code'] = Response::HTTP_OK;
    }

    public function setMessage($message)
    {
        $this->response['message'] = $message;
    }

    public function setStatus($code)
    {
        $this->response['status'] = $code;
    }

    public function setData($data)
    {
        $this->response['data'] = $data;
    }

    public function setCode($code)
    {
        $this->response['code'] = $code;
    }

    public function setAlert($alert)
    {
        $this->response['alert'] = $alert;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        return (isset($model->is_deleted) && $model->is_deleted == self::IS_DELETED) ? false : $model;
    }

    public function updateById($id, array $attributes)
    {
        $model = $this->find($id);
        if ($model) {
            $model->update($attributes);
            return $model;
        }
        return false;
    }

    public function delete($id)
    {
        $attributes = [
            'is_deleted' => self::IS_DELETED,
            'updated_by' => auth(ADMIN)->user()->id
        ];
        return $this->updateById($id, $attributes);
    }
}
