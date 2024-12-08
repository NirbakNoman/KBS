<?php


namespace App\Repositories;


use App\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PermissionRepository implements RepositoryInterface
{
    /**
     * @return mixed
     */
    public function all()
    {
        return Permission::orderBy('id', 'DESC')->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {

        return Permission::findOrFail($id);

    }

    /**
     * @param array $data
     * @return Builder|Model
     */
    public function create(array $data)
    {
        return Permission::create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        return Permission::find($id)->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return Permission::find($id)->delete();
    }


    /**
     * @return mixed
     */
    public function getWithPagination()
    {
        return Permission::orderBy('id', 'DESC')->paginate(10);
    }
}
