<?php


namespace App\Services;


use App\Helpers\Helper;
use App\Repositories\QuizRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuizService
{
    /**
     * @var QuizRepository
     */
    protected $quizRepository;


    /**
     * QuizFormService constructor.
     * @param QuizRepository $quizRepository
     */
    public function __construct(QuizRepository $quizRepository)
    {

        $this->quizRepository = $quizRepository;

    }


    /**
     * @return JsonResponse
     */
    public function getAll()
    {
        return response()->json(['status_code' => 302, 'messages'=>config('status.status_code.302'), 'quiz_form_list'=>$this->quizRepository->all()]);
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function getById($id)
    {

        if($this->quizRepository->get($id))
            return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'quiz_info'=>$this->quizRepository->get($id)]);

        return response()->json(['status_code' => 302, 'messages'=>config('status.status_code.302'), 'quiz_info'=>"Data not found"]);

    }


    /**
     * @param $request
     * @return JsonResponse
     */
    public function createItem($request)
    {
        $validator = Validator::make($request->all(),[

            'article_id' => 'required',
            'quiz_form_id' => 'required',
            'name' => 'required|unique:quizzes,name',
            'duration' => 'required',
            'total_marks' => 'required',
            'number_of_questions' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => '400', 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);
        }

        $input = $request->all();
        $input['slug'] = Helper::slugify($input['name']);
        $input['id'] = time().rand(1000,9000);

        $this->quizRepository->create($input);

        return response()->json(['status_code' => 201, 'messages'=>config('status.status_code.201')]);
    }


    /**
     * @param $request
     * @param $id
     * @return JsonResponse
     */
    public function updateItem($request)
    {

        $validator = Validator::make($request->all(),[

            'article_id' => 'required',
            'quiz_form_id' => 'required',
            'name' => 'required',
            'duration' => 'required',
            'total_marks' => 'required',
            'number_of_questions' => 'required',
            'status' => 'required',

        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => '400', 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);
        }

        DB::beginTransaction();

        try {

            $input = $request->all();
            $input['slug'] = Helper::slugify($input['name']);

            $this->quizRepository->update($input, $request->id);

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Found Exception: ' . $e->getMessage() . ' [Script: ' . __CLASS__ . '@' . __FUNCTION__ . '] [Origin: ' . $e->getFile() . '-' . $e->getLine() . ']');

            return response()->json(['status_code' => '424', 'messages'=>config('status.status_code.424'), 'error' => $e->getMessage()]);
        }

        DB::commit();

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200')]);

    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function deleteItem($id)
    {

        DB::beginTransaction();

        try {

            $this->quizRepository->delete($id);

        } catch (Exception $e) {

            DB::rollBack();

            Log::error('Found Exception: ' . $e->getMessage() . ' [Script: ' . __CLASS__ . '@' . __FUNCTION__ . '] [Origin: ' . $e->getFile() . '-' . $e->getLine() . ']');

            return response()->json(['status_code' => '424', 'messages'=>config('status.status_code.424'), 'error' => $e->getMessage()]);
        }

        DB::commit();

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200')]);

    }


    /**
     * @return JsonResponse
     */
    public function paginateData($request)
    {

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'quiz_list'=>$this->quizRepository->getWithPagination($request)]);

    }
}
