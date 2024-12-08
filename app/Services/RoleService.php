<?php


namespace App\Services;


use App\Helpers\Helper;
use App\Repositories\RoleRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class RoleService
{

    /**
     * @var RoleRepository
     */
    protected $roleRepository;


    /**
     * RoleService constructor.
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }


    /**
     * @return JsonResponse
     */
    public function getAll()
    {
        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'role_list'=>$this->roleRepository->all()]);
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function getById($id)
    {

        if($this->roleRepository->get($id))
            return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'role_info'=>$this->roleRepository->get($id)]);

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'role_info'=>"Data not found"]);

    }


    /**
     * @param $request
     * @return JsonResponse
     */
    public function createItem($request)
    {

        $validator = Validator::make($request->all(),[

            'name' => 'required|unique:roles,name',
            'permission' => 'required',

        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => '400', 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);

        }

        DB::beginTransaction();

        try {

            $role = $this->roleRepository->create(['name' => $request->input('name'), 'slug' => Helper::slugify($request->input('name'))]);

            $role->permissions()->attach($request->input('permission'));

        } catch (Exception $e) {

            DB::rollBack();
            Log::info($e->getMessage());

            return response()->json(['status_code' => '424', 'messages'=>config('status.status_code.424'), 'error' => $e->getMessage()]);
        }

        DB::commit();

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200')]);

    }


    /**
     * @param $request
     * @return JsonResponse
     */
    public function updateItem($request)
    {

        $validator = Validator::make($request->all(),[

            'name' => 'required',
            'permission' => 'required',

        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => '400', 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);

        }

        DB::beginTransaction();

        try {

            $this->roleRepository->update(['name' => $request->input('name'), 'slug' => Helper::slugify($request->input('name'))], $request->id);
            $role = $this->roleRepository->get($request->id);
            DB::table('roles_permissions')->where('role_id', $request->id)->delete();
            $role->permissions()->attach($request->input('permission'));

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

            $this->roleRepository->delete($id);

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

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'role_list'=>$this->roleRepository->getWithPagination()]);

    }

}
