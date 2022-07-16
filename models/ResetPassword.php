<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\User;

class ResetPassword extends ActiveRecord{

    public $password;
    public $repeat_password;

    public function rules()
    {
        return [
         [['password','repeat_password'],'required'],
         [['password','repeat_password'],'filter','filter' => 'trim'],
         [['password','repeat_password'],'string','min' => 6],
         ['repeat_password','compare','compareAttribute' => 'password'],
        ];
    }

    public function attributeLabels()
    {
        return [
         'password' => 'Пароль',
         'repeat_password' => 'Повторить пароль'
        ];
    }

    public function passwordRecovery($token)
    {
        if(empty($token)){
            throw new \DomainException('Пустой токен');
        }

        $user=User::findOne(['reset_token' => $token]);
        if(!$user){
            throw new \DomainException('Пользователь не найден');
        }

        $limit_date=strtotime('+1 day',$user->reset_time);
        $now=time();
        if($now>$limit_date){
            $user->reset_token=null;
            $user->reset_time=null;
            if($user->save()){
                throw new \RuntimeException('Время истекло попробуите занова восстановить пароль');
            }
        }

        return $user;
    }

    public function setRecoveryPassword(User $user)
    {
        $user->setHashPassword($this->password);
        $user->reset_token=null;
        $user->reset_time=null;
        
        if(!$user->save()){
            throw new \RuntimeException('Произошла ошибка при восстановление пароля');
        }
    }




}

?>