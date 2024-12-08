<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserService
{

    /**
     * @var UserRepository
     */
    protected $userRepository;


    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {

        $this->userRepository = $userRepository;

    }


    /**
     * @return JsonResponse
     */
    public function getAll()
    {

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'user_list'=>$this->userRepository->all()]);

    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function getById($id)
    {

        if($this->userRepository->get($id))
            return response()->json([
                'status_code' => 200,
                'messages'=>config('status.status_code.200'),
                'user_info'=>$this->userRepository->get($id)
            ]);

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'user_info'=>"Data not found"]);

    }


    /**
     * @param $request
     * @return JsonResponse
     */
    public function createItem($request)
    {

        $validator = Validator::make($request->all(),[

            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:60|unique:users',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'

        ]);

        if($validator->fails()) {

           return response()->json([
               'status_code' => '400',
               'messages'=>config('status.status_code.400'),
               'error' =>  $validator->errors()->first()
           ]);

        }

        if(isset($request->image_file)) {

            $thumbFile = Helper::base64ProfileImageThumbUpload("avatar", $request->image_file);

        } else {

            $thumbFile = url('/').'/media/avatar/placeholder.png';
        }


        $input = $request->all();
        $input['id'] = time().rand(1000,9000);
        $input['password'] = Hash::make($request->password);

        DB::beginTransaction();

        try {

            $this->userRepository->create($input);
            $user = $this->getItemById($input['id']);

            $user->roles()->attach($request->input('roles'));

            $user->media()->create([

                'url' => $thumbFile

            ]);

        } catch (Exception $e) {

            DB::rollBack();
            Log::info($e->getMessage());

            return response()->json(['status_code' => 424, 'messages'=>config('status.status_code.424'), 'error' => $e->getMessage()]);
        }

        DB::commit();

        return response()->json(['status_code' => 201, 'messages'=>config('status.status_code.201')]);


    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function updateItem($request)
    {
        $validator = Validator::make($request->all(),[

            'first_name' => 'required|string|max:60',
            'last_name' => 'required|string|max:60',
            'username' => 'required|string|max:50',
            'email' => 'required|string|email|max:60',
            'roles' => 'required'

        ]);

        if($validator->fails()) {

            return response()->json(['status_code' => 400, 'messages'=>config('status.status_code.400'), 'error' =>  $validator->errors()->first()]);
        }


        $input = $request->all();
        $input['slug'] = Helper::slugify($request->first_name.$request->last_name) ;

        if(!empty($input['password'])) {

            $input['password'] = Hash::make($input['password']);

        } else {

            $input = Arr::except($input,array('password'));

        }

        DB::beginTransaction();

        try {

            $this->userRepository->update($input, $input['id']);
            $user = $this->getItemById($input['id']);

            DB::table('users_roles')->where('user_id', $input['id'])->delete();
            DB::table('users_permissions')->where('user_id', $input['id'])->delete();

            $user->roles()->attach($request->input('roles'));


        } catch (Exception $e) {

            DB::rollBack();
            Log::info($e->getMessage());

            return response()->json(['status_code' => 424, 'messages'=>config('status.status_code.424'), 'error' => $e->getMessage()]);
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

            $this->userRepository->delete($id);

        } catch (Exception $e) {

            DB::rollBack();

            Log::info($e->getMessage());

            return response()->json(['status_code' => 424, 'messages'=>config('status.status_code.424'), 'error' => $e->getMessage()]);
        }

        DB::commit();

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200')]);
    }


    /**
     * @return JsonResponse
     */
    public function paginateData()
    {

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'user_list'=>$this->userRepository->getWithPagination()]);

    }


    /**
     * @param $id
     * @return mixed
     */
    public function getItemById($id)
    {

        return $this->userRepository->getUser($id);
    }

}
