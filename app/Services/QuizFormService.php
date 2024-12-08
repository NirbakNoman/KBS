<?php


namespace App\Services;


use App\Helpers\Helper;
use App\Repositories\QuizFormRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class QuizFormService
{

    /**
     * @var QuizFormRepository
     */
    protected $quizFormRepository;


    /**
     * QuizFormService constructor.
     * @param QuizFormRepository $quizFormRepository
     */
    public function __construct(QuizFormRepository $quizFormRepository)
    {

        $this->quizFormRepository = $quizFormRepository;

    }


    /**
     * @return JsonResponse
     */
    public function getAll()
    {
        return response()->json(['status_code' => 302, 'messages'=>config('status.status_code.302'), 'quiz_form_list'=>$this->quizFormRepository->all()]);
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function getById($id)
    {

        if($this->quizFormRepository->get($id))
            return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'quiz_form_info'=>$this->quizFormRepository->get($id)]);

        return response()->json(['status_code' => 302, 'messages'=>config('status.status_code.302'), 'quiz_form_info'=>"Data not found"]);

    }


    /**
     * @param $request
     * @return JsonResponse
     */
    public function createItem($request)
    {
        $validator = Validator::make($request->all(),[

            'name' => 'required|unique:quiz_forms,name',

        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => '400', 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);
        }

        $input = $request->all();
        $input['slug'] = Helper::slugify($input['name']);
        $input['id'] = time().rand(1000,9000);

        $this->quizFormRepository->create($input);

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

            'name' => 'required',

        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => '400', 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);
        }

        DB::beginTransaction();

        try {

            $input = $request->all();
            $input['slug'] = Helper::slugify($input['name']);

            $this->quizFormRepository->update($input, $request->id);

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

            $this->quizFormRepository->delete($id);

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
    public function paginateData()
    {

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'quiz_form_list'=>$this->quizFormRepository->getWithPagination()]);

    }
}
