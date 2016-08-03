<?php
namespace App\Usecase;

class ImageUsecase{

    //ファイル名を返す
    public function saveTmpFile($path,$image){
      // 重複しない様UNIXタイムを名前にファイルを保存する
      $file_extention= pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);
      $save_name=(string)(microtime(true)*10000).".".$file_extention;
      $image->moveTo($path.$save_name);
      return $save_name;
    }
    //公開パスへ移動する
    public function saveAsPublicFile($tmp_path,$real_path,$file_name){
      rename($tmp_path.$file_name,$real_path.$file_name);
      return true;
    }

}
