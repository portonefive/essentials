<?php
/**
 * Created by PhpStorm.
 * User: Jeff
 * Date: 5/14/15
 * Time: 10:17 PM
 */
namespace PortOneFive\Essentials\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface Repository
{

    /**
     * @param array         $columns
     * @param array         $with
     * @param callable|null $callback
     *
     * @return Collection|\Illuminate\Database\Eloquent\Model[]
     */
    public function all($columns = ['*'], $with = [], $callback = null);

    /**
     * @param null     $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param callable $callback
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $callback = null);

    /**
     * @param       $id
     * @param array $columns
     *
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, $columns = ['*']);

    /**
     * @param       $id
     * @param array $columns
     *
     * @return \Illuminate\Support\Collection|null
     */
    public function find($id, $columns = ['*']);

    /**
     * @param array    $attributes
     * @param callable $callback
     * @param array    $saveOptions
     *
     * @return Model
     */
    public function create(array $attributes, $callback = null, $saveOptions = []);

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function newInstance(array $attributes = []);

    /**
     * @param      $column
     * @param null $key
     *
     * @return Collection
     */
    public function lists($column, $key = null);

    /**
     * @param Model $model
     * @param array $attributes
     *
     * @return bool
     */
    public function update(Model $model, array $attributes);

    /**
     * @param Model $model
     *
     * @return bool|null
     * @throws \Exception
     */
    public function destroy(Model $model);
}
