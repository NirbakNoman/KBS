<?php


namespace App\Services;


use App\Helpers\Helper;
use App\Repositories\QuizFormFieldRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuizFormFieldService
{
    /**
     * @var quizFormFieldRepository
     */
    protected $quizFormFieldRepository;


    /**
     * QuizFormService constructor.
     * @param QuizFormFieldRepository $quizFormFieldRepository
     */
    public function __construct(QuizFormFieldRepository $quizFormFieldRepository)
    {

        $this->quizFormFieldRepository = $quizFormFieldRepository;

    }


    /**
     * @return JsonResponse
     */
    public function getAll()
    {
        return response()->json(['status_code' => 302, 'messages'=>config('status.status_code.302'), 'quiz_form_field_list'=>$this->quizFormFieldRepository->all()]);
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function getById($id)
    {

        if($this->quizFormFieldRepository->get($id))
            return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'quiz_form_field_info'=>$this->quizFormFieldRepository->get($id)]);

        return response()->json(['status_code' => 302, 'messages'=>config('status.status_code.302'), 'quiz_form_field_info'=>"Data not found"]);

    }


    /**
     * @param $request
     * @return JsonResponse
     */
    public function createItem($request)
    {
        $validator = Validator::make($request->all(),[

            'quiz_form_id' => 'required',
            'f_label' => 'required',
            'f_name' => 'required',
            'f_id' => 'required',
            'f_class' => 'required',
            'f_type' => 'required',

        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => '400', 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);
        }

        $input = $request->all();

        $input['id'] = time().rand(1000,9000);

        $this->quizFormFieldRepository->create($input);

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

            'quiz_form_id' => 'required',
            'f_label' => 'required',
            'f_name' => 'required',
            'f_id' => 'required',
            'f_class' => 'required',
            'f_type' => 'required',

        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => '400', 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);
        }

        DB::beginTransaction();

        try {

            $input = $request->all();

            $this->quizFormFieldRepository->update($input, $request->id);

        } catch (Exception $e) {

            DB::rollBack();
            Log::info($e->getMessage());

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

            $this->quizFormFieldRepository->delete($id);

        } catch (Exception $e) {

            DB::rollBack();

            Log::info($e->getMessage());

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

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'quiz_form_field_list'=>$this->quizFormFieldRepository->getWithPagination()]);

    }
}
