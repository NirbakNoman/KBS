<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Services\FaqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    protected $faqService;
    public function __construct(FaqService $faqService)
    {
        $this->middleware('auth');
        $this->faqService = $faqService;

    }

    public function index(Request $request)
    {
        if(Auth::user()->can('faq-list')) {

            return $this->faqService->paginateData($request);

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

        if(Auth::user()->can('faq-create')) {
            //dd("hello");
            return $this->faqService->createItem($request);

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
        //
    }


    public function show($id)
    {
        if(Auth::user()->can('faq-list')) {

            return $this->faqService->getById($id);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }

    public function update(Request $request)
    {

        if(Auth::user()->can('faq-edit')) {

            return $this->faqService->updateItem($request);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }


    public function destroy(Request $request)
    {
        if(Auth::user()->can('faq-delete')) {

            return  $this->faqService->deleteItem($request->id);

        } else {

            return response()->json(['status_code' => 424, 'messages'=>'User does not have the right permissions']);

        }

    }
}
