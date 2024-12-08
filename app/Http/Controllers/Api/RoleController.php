<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Auth;

class RoleController extends Controller
{

    /**
     * @var RoleService
     * @var permissionService
     */
    protected $roleService;
    protected $permissionService;


    /**
     * RoleController constructor.
     * @param RoleService $roleService
     * @param PermissionService $permissionService
     */
    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {

        $this->middleware('auth');
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;

    }


    /**
     * @return mixed
     */
    public function index()
    {
        if(Auth::user()->can('role-list')) {

            return $this->roleService->paginateData();

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }


    /**
     * @return void
     */
    public function create()
    {

        /*$permissions = $this->permissionService->getAll();

        return view('roles.create',compact('permissions'));*/

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        if(Auth::user()->can('role-create')) {

            return $this->roleService->createItem($request);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }


    }


    /**
     * @param $id
     * @return void
     */
    public function edit($id)
    {

        /*$roles = $this->roleService->getById($id);

        $permissions = $this->permissionService->getAll();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();


        return view('roles.edit',compact('roles','permissions','rolePermissions'));*/

    }


    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {

        if(Auth::user()->can('role-list')) {

            return $this->roleService->getById($id);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }


    }


    /**
     * @param Request $request
     * @return mixed
     *
     */
    public function update(Request $request)
    {

        if(Auth::user()->can('role-edit')) {

            return $this->roleService->updateItem($request);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function destroy(Request $request)
    {

        if(Auth::user()->can('role-delete')) {

            return  $this->roleService->deleteItem($request->id);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }
}
