<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Http\Traits\QueryTrait;
use App\Models\Faq;

class FaqRepository implements RepositoryInterface
{
    use QueryTrait;
    /**
     * @return mixed
     */
    public function all()
    {

        return Faq::with( 'user', 'category')
        ->orderBy('en_title', 'ASC')
        ->get();

    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return Faq::with( 'user', 'category')->find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {

        $dataObj =  new Faq;
        $dataObj->id = $data['id'];
        $dataObj->user_id = $data['user_id'];
        $dataObj->category_id = $data['category_id'];
        $dataObj->en_title = $data['en_title'];
        //$dataObj->bn_title = $data['bn_title'];
        $dataObj->tag = $data['tag'];
        $dataObj->slug = Helper::slugify($data['en_title']);
        $dataObj->en_body = $data['en_body'];
        //$dataObj->bn_body = $data['bn_body'] ?? null;
        $dataObj->status = $data['status'];
        $dataObj->publish_date = $data['publish_date'];

        return $dataObj->save();

    }


    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {

        return Faq::find($id)->update($data);

    }


    /**
     * @param $id
     */
    public function delete($id)
    {

        return Faq::find($id)->delete();

    }

    /**
     * @param $request
     * @return mixed
     */
    public function getWithPagination($request)
    {

        $query = Faq::with( 'user', 'category');
        $whereFilterList = ['category_id', 'status'];
        $likeFilterList  = ['en_title', 'tag'];
        $query = self::filterFaq($request, $query, $whereFilterList, $likeFilterList);

        return $query->orderBy('en_title', 'ASC')
            ->paginate(10);

    }

    /**
     * @param $request
     * @param $query
     * @param array $whereFilterList
     * @param array $likeFilterList
     * @return mixed
     */
    public static function filterFaq($request, $query, array $whereFilterList, array $likeFilterList)
    {
        $query = self::likeQueryFilter($request, $query, $likeFilterList);
        $query = self::whereQueryFilter($request, $query, $whereFilterList);

        if($request->filled('publish_date')) {

            $query->whereDate('publish_date', '=', $request->publish_date);
        }

        return $query;

    }

}
