<?php


namespace App\Repositories;


use App\Helpers\Helper;
use App\Models\QuizForm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuizFormRepository
{

    /**
     * @return mixed
     */
    public function all()
    {

        return QuizForm::orderBy('created_at', 'DESC')->get();

    }


    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return QuizForm::findOrFail($id);
    }


    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {

        $dataObj =  new QuizForm;
        $dataObj->id = $data['id'];
        $dataObj->name = $data['name'];
        $dataObj->slug = $data['slug'];

        return $dataObj->save();
    }


    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        return QuizForm::find($id)->update($data);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return QuizForm::find($id)->delete();
    }


    /**
     * @return LengthAwarePaginator
     */
    public function getWithPagination()
    {
        return QuizForm::with('quizFormField')->orderBy('created_at', 'DESC')->paginate(10);
    }

}
