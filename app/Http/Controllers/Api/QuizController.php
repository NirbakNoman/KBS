<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{

    private $quizService;


    /**
     * PermissionController constructor.
     * @param QuizService $quizService
     */
    public function __construct(QuizService $quizService)
    {
        $this->middleware('auth');
        $this->quizService = $quizService;

    }

    /**
     * @return mixed
     */
    public function index(Request $request)
    {

        if(Auth::user()->can('quiz-list')) {

            return $this->quizService->paginateData($request);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }

    /**
     *
     */
    public function create()
    {

        /*$permissions = $this->quizService->getAll();
        return view('permissions.create',compact('permissions'));*/

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        if(Auth::user()->can('quiz-create')) {

            return $this->quizService->createItem($request);

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

        /*$quiz-forms = $this->quizService->getById($id);

        return view('quiz-forms.edit',compact('quiz-forms'));*/

    }


    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {

        if(Auth::user()->can('quiz-list')) {

            return $this->quizService->getById($id);

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

        if(Auth::user()->can('quiz-edit')) {

            return $this->quizService->updateItem($request);

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

        if(Auth::user()->can('quiz-delete')) {

            return $this->quizService->deleteItem($request->id);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }
}
