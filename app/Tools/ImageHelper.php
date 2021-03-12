<?php
/**
 * Created by PhpStorm.
 * User: adnan
 * Date: 11/03/2021
 * Time: 10:28 ص
 */

namespace App\Tools;


use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait ImageHelper
{
    public function uploadImg($string, $width=null, $height=null, $ratio=false, $old=null)
    {
       // return Image::make($string)->extension;
        if(empty($string)) return $old;
        if($width==null){
            $width= Image::make($string)->width();
        }
        if (Str::contains($string, 'base64')) {

            $ex = explode('/', explode(';', $string)[0])[1];
            $name =  'images/' . uniqid() . '.' . $ex;
            if ($width === null) {
                $width = Image::make($string)->width();
            }
            Image::make($string)->resize($width, $height, function ($r) use ($ratio) {
                if ($ratio === true) {
                    $r->aspectRatio();
                }
            })->save(storage_path('app/public/' . $name));
            return $name;
        } else {
            return $old;
        }


    }

}