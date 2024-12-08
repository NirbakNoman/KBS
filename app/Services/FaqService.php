<?php


namespace App\Services;

use App\Helpers\Helper;
use App\Repositories\FaqRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FaqService
{
    /**
     * @var FaqRepository
     */
    protected $faqRepository;

    public function __construct(FaqRepository $faqRepository)
    {

        $this->faqRepository = $faqRepository;

    }

    /**
     * @return JsonResponse
     */
    public function getAll()
    {

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'faq_list'=>$this->faqRepository->all()]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createItem(Request $request)
    {
        $validator = Validator::make($request->all(),[

            'en_title' => 'required|string',

       ]);

        if($validator->fails()) {

            return response()->json([
                'status_code' => '400',
                'messages'=>config('status.status_code.400'),
                'error' =>  $validator->errors()->first()
            ]);

        }

        $input = $request->all();
        $input['id'] = time().rand(1000,9000);
        $input['user_id'] = auth()->user()->id;
        $input['publish_date'] = date('Y-m-d H:i:s');

        DB::beginTransaction();

        try {

            $this->faqRepository->create($input);

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Found Exception: ' . $e->getMessage() . ' [Script: ' . __CLASS__ . '@' . __FUNCTION__ . '] [Origin: ' . $e->getFile() . '-' . $e->getLine() . ']');

            return response()->json(['status_code' => '424', 'messages'=>config('status.status_code.424'), 'error' => $e->getMessage()]);
        }

        DB::commit();

        return response()->json(['status_code' => 201, 'messages'=>config('status.status_code.201')]);

    }


    public function getItemById($id)
    {

        return $this->faqRepository->get($id);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getById($id)
    {
        if($this->faqRepository->get($id))
            return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'faq_info'=>$this->faqRepository->get($id)]);

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'faq_info'=>"Data not found"]);

    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function updateItem($request)
    {

        $validator = Validator::make($request->all(),[
            'en_title' => 'required|string',
        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => '400', 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);

        }
        $input = $request->all();
        $input['slug'] = Helper::slugify($input['en_title']);

        DB::beginTransaction();

        try {

            $this->faqRepository->update($input, $input['id']);

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

            $this->faqRepository->delete($id);

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
    public function paginateData(Request $request)
    {

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'faq_list'=>$this->faqRepository->getWithPagination($request)]);

    }

}
