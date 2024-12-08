<?php


namespace App\Services;


use App\Helpers\Helper;
use App\Repositories\PermissionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PermissionService
{

    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;


    /**
     * PermissionService constructor.
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }


    /**
     * @return JsonResponse
     */
    public function getAll()
    {
        return response()->json(['status_code' => 302, 'messages'=>config('status.status_code.302'), 'permission_list'=>$this->permissionRepository->all()]);
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function getById($id)
    {

        if($this->permissionRepository->get($id))
            return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'permission_info'=>$this->permissionRepository->get($id)]);

        return response()->json(['status_code' => 302, 'messages'=>config('status.status_code.302'), 'permission_info'=>"Data not found"]);

    }


    /**
     * @param $request
     * @return JsonResponse
     */
    public function createItem($request)
    {
        $validator = Validator::make($request->all(),[

            'name' => 'required|unique:permissions,name',

        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => '400', 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);
        }

        $input = $request->all();
        $input['slug'] = Helper::slugify($input['name']);

        $this->permissionRepository->create($input);

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

            $this->permissionRepository->update($input, $request->id);

        } catch (Exception $e) {

            DB::rollBack();
            Log::info($e->getMessage());

            return response()->json(['status_code' => '424', 'messages'=>config('status.status_code.424'), 'error' => $e->getMessage()]);
        }

        DB::commit();

        return response()->json(['status_code' => 201, 'messages'=>config('status.status_code.201')]);

    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function deleteItem($id)
    {

        DB::beginTransaction();

        try {

            $this->permissionRepository->delete($id);

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

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'user_list'=>$this->permissionRepository->getWithPagination()]);

    }
}
