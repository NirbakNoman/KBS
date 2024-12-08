<?php


namespace App\Helpers;

use Auth;
use Image;

class Helper
{

    public static function slugify($value)
    {

        return strtolower(str_replace(' ','-',$value));

    }

    /**
     * @param $url
     * @param $image
     * @return string
     */
    public static function base64ProfileImageThumbUpload($url, $image)
    {
        $fileName = time () .".jpg";
        $dir = "media/".$url."/".Auth::user()->id."/thumbs/";

        if (!file_exists(public_path ($dir))) {

            mkdir(public_path ($dir), 0755, true);

        }

        $small_image = Image::make($image);
        $thumbImage = $small_image->resize(null, 250, function ($e) {

            $e->aspectRatio();

        });

        $thumbImage->save(public_path($dir. $fileName ));

        return url('/').'/'.$dir.$fileName;
    }


    /**
     * @param $url
     * @param $image
     * @return string
     */
    public static function base64ImageUpload($url, $image)
    {
        $fileName = uniqid().".jpg";
        $dir = "media/".$url."/";

        if (!file_exists(public_path ($dir))) {

            mkdir(public_path ($dir), 0755, true);

        }

        $small_image = Image::make($image);
        $thumbImage = $small_image->resize(null, 500, function ($e) {

            $e->aspectRatio();

        });

        $thumbImage->save(public_path($dir. $fileName ));

        return url('/').'/'.$dir.$fileName;
    }

}
