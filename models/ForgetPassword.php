<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\User;
use Yii;


class ForgetPassword extends ActiveRecord
{
    public $email;


    public function rules()
    {
        return [
            ['email','required'],
            ['email','email'],
            ['email','exist','targetClass' => 'app\models\User','targetAttribute' => 'email','message' => 'Такой E-mail не существует'],
            ['email','checkTime'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'E-mail',
        ];
    }

    public function getUser()
    {
       return User::findOne(['email' => $this->email]);
    }

    public function setResetToken()
    {
        $user=$this->getUser();
        if(!$user){
            throw new \DomainException('User is not found');
        }

        $user->reset_token=Yii::$app->getSecurity()->generateRandomString();
        $user->reset_time=time();
        if(!$user->save()){
            throw new \RuntimeException('Saving error');
        }

        return $user;
    }

    public function sendMsgResetPassword(User $user)
    {
        $resetUrl=Yii::$app->urlManager->createAbsoluteUrl(['main/reset-password','token' => $user->reset_token]);

        $send_email=Yii::$app->mailer->compose('reset',['resetUrl' => $resetUrl])
        ->setFrom(Yii::$app->params['senderEmail'])
        ->setTo($user->email)
        ->setSubject('Сброс пароля от '.$_SERVER['SERVER_NAME'])
        ->send();

        if(!$send_email){
            throw new \RuntimeException('Reset password sending error');
        }
    }

    public function checkTime($attribute,$params)
    {
      $user=$this->getUser();
      if($user->reset_time!==null){
        $limit_time=strtotime('+6 hours',$user->reset_time);
        $now=time();
        if($limit_time>$now) $this->addError($attribute,'Восстановление пароля возможен каждые 6 часов');
      }
      
    }


}


?>