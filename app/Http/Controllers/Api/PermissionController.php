<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Auth;

class PermissionController extends Controller
{

    /**
     * @var PermissionService
     */
    protected $permissionService;


    /**
     * PermissionController constructor.
     * @param PermissionService $permissionService
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->middleware('auth');
        $this->permissionService = $permissionService;

    }


    /**
     * @return mixed
     */
    public function index()
    {

        if(Auth::user()->can('quiz-form-list')) {

            return $this->permissionService->paginateData();

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }


    /**
     *
     */
    public function create()
    {

        /*$permissions = $this->permissionService->getAll();
        return view('permissions.create',compact('permissions'));*/

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        if(Auth::user()->can('quiz-form-create')) {

            return $this->permissionService->createItem($request);

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

        /*$permissions = $this->permissionService->getById($id);

        return view('permissions.edit',compact('permissions'));*/

    }


    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {

        if(Auth::user()->can('quiz-form-list')) {

            return $this->permissionService->getById($id);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {

        if(Auth::user()->can('quiz-form-edit')) {

            return $this->permissionService->updateItem($request);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {

        if(Auth::user()->can('quiz-form-delete')) {

            return $this->permissionService->deleteItem($request->id);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }
}
