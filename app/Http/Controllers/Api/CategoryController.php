<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{

    /**
     * @var CategoryService
     */
    protected $categoryService;


    /**
     * CategoryController constructor.
     * @param CategoryService $categoryService
     *
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->middleware('auth');
        $this->categoryService = $categoryService;
    }


    /**
     * @return mixed
     */
    public function index(Request $request)
    {

        if(Auth::user()->can('category-list')) {

            if ($request->filled('isAdmin'))
                return $this->categoryService->paginateData($request);
            else
                return $this->categoryService->getAll();

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }

    /**
     * @return void
     */
    public function create()
    {
        /*return view('categorys.create',compact('permissions'));*/

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        if(Auth::user()->can('category-create')) {

            return $this->categoryService->createItem($request);

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

        /*$categorys = $this->categoryService->getById($id);

        $permissions = $this->permissionService->getAll();
        $categoryPermissions = DB::table("category_has_permissions")->where("category_has_permissions.category_id",$id)
            ->pluck('category_has_permissions.permission_id','category_has_permissions.permission_id')
            ->all();


        return view('categorys.edit',compact('categorys','permissions','categoryPermissions'));*/

    }


    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        if(Auth::user()->can('category-list')) {

            return $this->categoryService->getById($id);

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
        //dd($request->all());

        if(Auth::user()->can('category-edit')) {

            return $this->categoryService->updateItem($request);

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
        //dd($request->id);
        if(Auth::user()->can('category-delete')) {

            return  $this->categoryService->deleteItem($request->id);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);
        }

    }
}
