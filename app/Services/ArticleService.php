<?php


namespace App\Services;

use App\Helpers\Helper;
use App\Models\Media;
use App\Repositories\ArticleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ArticleService
{
    /**
     * @var ArticleRepository
     */
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {

        $this->articleRepository = $articleRepository;

    }

    /**
     * @return JsonResponse
     */
    public function getAll()
    {

        return response()->json(['status_code' => 200, 'messages'=>config('status.status_code.200'), 'article_list'=>$this->articleRepository->all()]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createItem(Request $request)
    {

        $thumbFile = [];

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

            $this->articleRepository->create($input);
            $article = $this->getItemById($input['id']);

            if(isset($request->image_file)) {

                $thumbImageList = $request->image_file;

                if(count($thumbImageList) > 0) {
                    for ($i = 1; $i <= count($thumbImageList); $i++) {
                        $thumbFile[] = Helper::base64ImageUpload("article/images", $thumbImageList[count($thumbImageList)-$i]);
                    }
                }
            }

            foreach ($thumbFile as $image):

                $article->media()->create([

                    'url' => $image

                ]);

            endforeach;

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

        return $this->articleRepository->get($id);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getById($id)
    {
        if($this->articleRepository->get($id))
            return response()->json([
                'status_code' => 200,
                'messages'=>config('status.status_code.200'),
                'article_info'=>$this->articleRepository->get($id)
            ]);

        return response()->json([
            'status_code' => 200,
            'messages'=>config('status.status_code.200'),
            'article_info'=>"Data not found"
        ]);

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

            $this->articleRepository->update($input, $input['id']);

            if(isset($request->image_file)) {

                $article = $this->getItemById($request->id);

                $mediaList = Media::where('mediable_id', $request->id)->get();
                if (count($mediaList) > 0)
                {
                    foreach($mediaList as $media)
                    {
                        $mediaName =  substr($media->url, strpos($media->url, "media") );
                        unlink(public_path().'/'.$mediaName );
                        $media->delete();
                    }
                }

                $thumbImageList = $request->image_file;

                if(count($thumbImageList) > 0) {
                    for ($i = 1; $i <= count($thumbImageList); $i++) {
                        $thumbFile[] = Helper::base64ImageUpload("article/images", $thumbImageList[count($thumbImageList)-$i]);
                    }
                }

                foreach ($thumbFile as $image):

                    $article->media()->create([

                        'url' => $image

                    ]);

                endforeach;
            }


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
            $this->articleRepository->delete($id);

        } catch (Exception $e) {

            DB::rollBack();

            Log::error('Found Exception: ' . $e->getMessage() . ' [Script: ' . __CLASS__ . '@' . __FUNCTION__ . '] [Origin: ' . $e->getFile() . '-' . $e->getLine() . ']');

            return response()->json(['status_code' => '424', 'messages'=>config('status.status_code.424'), 'error' => $e->getMessage()]);
        }

        DB::commit();

        return response()->json([
            'status_code' => 200,
            'messages'=> "Article Deleted Successfully"
        ]);

    }

    /**
     * @return JsonResponse
     */
    public function paginateData($request)
    {

        return response()->json([
            'status_code' => 200,
            'messages'=>config('status.status_code.200'),
            'article_list'=>$this->articleRepository->getWithPagination($request)
        ]);

    }

}
