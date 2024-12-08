<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QuizFormService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class QuizFormController extends Controller
{
    /**
     * @var QuizFormService
     */
    protected $quizFromService;


    /**
     * PermissionController constructor.
     * @param QuizFormService $quizFromService
     */
    public function __construct(QuizFormService $quizFromService)
    {
        $this->middleware('auth');
        $this->quizFromService = $quizFromService;

    }


    /**
     * @return mixed
     */
    public function index()
    {

        if(Auth::user()->can('quiz-form-list')) {

            return $this->quizFromService->paginateData();

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }


    /**
     *
     */
    public function create()
    {

        /*$permissions = $this->quizFromService->getAll();
        return view('permissions.create',compact('permissions'));*/

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        if(Auth::user()->can('quiz-form-create')) {

            return $this->quizFromService->createItem($request);

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

        /*$quiz-forms = $this->quizFromService->getById($id);

        return view('quiz-forms.edit',compact('quiz-forms'));*/

    }


    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {

        if(Auth::user()->can('quiz-form-list')) {

            return $this->quizFromService->getById($id);

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

            return $this->quizFromService->updateItem($request);

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

            return $this->quizFromService->deleteItem($request->id);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }
}
