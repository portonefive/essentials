<?php namespace PortOneFive\Essentials\Repositories;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PortOneFive\Essentials\Contracts\Repositories\Repository as RepositoryContract;

abstract class Repository implements RepositoryContract
{

    /** @var Model */
    protected $model;
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param array         $columns
     * @param array         $with
     * @param callable|null $callback
     *
     * @return Collection|\Illuminate\Database\Eloquent\Model[]
     */
    public function all($columns = ['*'], $with = [], $callback = null)
    {
        $query = $this->newQuery()->with($with);

        $this->handleQueryCallback($query, $callback);

        return $query->get($columns);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function newQuery()
    {
        return $this->getModel()->newQueryWithoutScopes();
    }

    /**
     * @return Model
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * @param Builder $query
     * @param Closure $callback
     */
    protected function handleQueryCallback(Builder $query, $callback)
    {
        if ( ! is_null($callback) && is_callable($callback)) {
            call_user_func($callback, $query);
        }
    }

    /**
     * @param null     $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param callable $callback
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $callback = null)
    {
        $query = $this->newQuery();

        $this->handleQueryCallback($query, $callback);

        return $query->paginate($perPage, $columns, $pageName);
    }

    /**
     * @param       $id
     * @param array $columns
     *
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, $columns = ['*'])
    {
        return $this->newQuery()->findOrFail($id, $columns);
    }

    /**
     * @param       $id
     * @param array $columns
     *
     * @return \Illuminate\Support\Collection|null
     */
    public function find($id, $columns = ['*'])
    {
        return $this->getModel()->find($id, $columns);
    }

    /**
     * @param array    $attributes
     * @param callable $callback
     * @param array    $saveOptions
     *
     * @return Model
     */
    public function create(array $attributes, $callback = null, $saveOptions = [])
    {
        $new = $this->newInstance($attributes);

        if (is_callable($callback)) {
            call_user_func($callback, $new);
        }

        return $new->save($saveOptions) ? $new : false;
    }

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function newInstance(array $attributes = [])
    {
        return $this->getModel()->newInstance($attributes);
    }

    /**
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        $query = $this->newQuery();

        return call_user_func_array([$query, $method], $params);
    }

    /**
     * @param      $column
     * @param null $key
     *
     * @return Collection
     */
    public function lists($column, $key = null)
    {
        return $this->newQuery()->lists($column, $key);
    }

    /**
     * @param Model $model
     * @param array $attributes
     *
     * @return bool
     */
    public function update(Model $model, array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $model->{$key} = $value;
        }

        return $model->push();
    }

    /**
     * @param Model $model
     *
     * @return bool|null
     * @throws \Exception
     */
    public function destroy(Model $model)
    {
        return $model->delete();
    }
}
