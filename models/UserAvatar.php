<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;
use app\models\User;
use yii\web\UploadedFile;

class UserAvatar extends ActiveRecord
{

public $avatar;

const DEFAULT_AVATAR='default-avatar.png';

public function rules()
{
    return [
         ['avatar','file','extensions' => ['jpg','png','gif'],'maxSize' => 2*1024*1024 ],
    ];
}

public function uploadUserAvatar()
{
    $this->avatar=UploadedFile::getInstance($this,'avatar');

    if($this->avatar->name===null){
        throw new \DomainException('Нельзя загрузить пустой аватар');
    }

    $user=User::findOne(Yii::$app->user->id);

    if(!$user){
        throw new \DomainException('Пользователь не найден');
    }

    if(!$this->uploadAvatar($user)){
        throw new \RuntimeException('Возникла ошибка повторите позже');
    }
}

public function uploadAvatar(User $user)
{
    if($this->validate()){
        $avatar_name=$this->avatar->baseName.'_'.Yii::$app->getSecurity()->generateRandomString(5).'.'.$this->avatar->extension;
        $this->avatar->saveAs('images/'.$avatar_name);
        $prev_avatar=$user->avatar;
        $user->avatar=$avatar_name;
        if($user->save()){
            if($prev_avatar!='') $this->deleteAvatar($prev_avatar);
            return true;
        }
    }
    return false;
}


public function deleteAvatar($avatar_name){
    $path=Yii::getAlias('@webroot').'/images';
    $files=scandir($path);
    $files=array_diff($files,['.','..']);
    if(in_array($avatar_name,$files) && $avatar_name!=self::DEFAULT_AVATAR){
        unlink($path.'/'.$avatar_name);
        return true;
    }
    return false;
}

public function setDefaultAvatar($id)
{
    $user=User::findOne($id);

    if($user){
        $user->avatar=self::DEFAULT_AVATAR;
        return $user->save(false);
    }
    
    return false;
}


}

?>