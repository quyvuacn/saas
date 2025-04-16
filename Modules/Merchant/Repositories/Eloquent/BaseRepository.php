<?php

namespace Modules\Merchant\Repositories\Eloquent;

use Illuminate\Http\Response;
use Modules\Merchant\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements EloquentRepositoryInterface
{
    protected $model;
    protected $response;

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
        return $this->model->find($id);
    }

    public function update($id, array $attributes)
    {
        $result = $this->find($id);
        if ($result) {
            $result->update($attributes);
            return $result;
        }
        return false;
    }

    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();

            return true;
        }
        return false;
    }
}
