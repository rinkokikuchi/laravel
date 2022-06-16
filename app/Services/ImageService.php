<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use InterventionImage;

class ImageService
{
    public static function upload($imageFile, $folderName)
    {
            $fileName = uniqid(rand().'_');              //ファイル名をrand関数（ランダム）を使用して作成
            $extension = $imageFile->extension();        //拡張機能追加
            $fileNameToStore = $fileName.'.'.$extension; //ファイル名＋拡張子
            $resizedImage = InterventionImage::make($imageFile)->resize(1920,1080)->encode(); //アップロードされた画面を変数に入れ、リサイズ（サイズ変換）＋encode
            Storage::put('public/'.$folderName.'/'.$fileNameToStore,$resizedImage);   //(フォルダ空のファイル名,リサイズした画像)

        return $fileNameToStore;
    }
}


?>