<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements RepositoryInterface
{
    /**
     * @return mixed
     */
    public function all()
    {

        return User::with(['roles' => function($q) {$q->select('id', 'name');}, 'media'])->orderBy('created_at', 'DESC')->get();

    }


    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {

        return User::with(['roles' => function($q) {$q->select('id', 'name');}, 'media'])->findOrFail($id);

    }


    public function getUser($id)
    {
        return User::findOrFail($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {

        $dataObj =  new User;
        $dataObj->id = $data['id'];
        $dataObj->username = $data['username'];
        $dataObj->first_name = $data['first_name'];
        $dataObj->last_name = $data['last_name'];
        $dataObj->slug = Helper::slugify($data['first_name'].$data['last_name']) ;
        $dataObj->email = $data['email'];
        $dataObj->password = $data['password'];

        return $dataObj->save();
    }


    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        return User::find($id)->update($data);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return User::find($id)->delete();
    }


    /**
     * @return LengthAwarePaginator
     */
    public function getWithPagination()
    {
        return User::with(['roles' => function($q) {$q->select('id', 'name');}, 'media'])->orderBy('created_at', 'DESC')->paginate(10);
    }

}
