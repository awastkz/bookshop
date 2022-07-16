<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\User;
use Yii;

class Register extends ActiveRecord
{

public $fio;
public $email;
public $password;
public $password_repeat;
public $captcha;
public $avatar;
public $status;
public $token;

const DEFAULT_AVATAR='default-avatar.png';


public function rules()
{
   return [
     [['fio','email','password','password_repeat'],'required'],
     ['email','email'],
     ['email','unique','targetClass' => 'app\models\User'],
     ['password','string','min' => 6],
     ['password_repeat','compare','compareAttribute' => 'password'],
     ['captcha','captcha','captchaAction' => 'main/captcha'],
     ['avatar','default','value' => self::DEFAULT_AVATAR],
   ];
}

public function attributeLabels()
{
   return [
      'fio' => 'ФИО',
      'email' => 'E-mail',
      'password' => 'Пароль',
      'password_repeat' => 'Подтверждение пароля',
      'captcha' => 'Капча',
   ];
}

public function registerUser()
{
   $user=new User();
   $user->fio=$this->fio;
   $user->email=$this->email;
   $user->setHashPassword($this->password);
   $user->avatar=$this->avatar;
   $user->role=User::ROLE_BASIC;
   $user->ip=Yii::$app->request->userIp;
   $user->status=User::STATUS_INACTIVE;
   $user->token=Yii::$app->getSecurity()->generateRandomString();
   $user->register_date=date('d-m-Y H:i');

   if(!$user->save()){
      throw new \RuntimeException('Register error');
   }

   return $user;
}

public function sendMsgConfirmEmail(User $user)
{

   $confirmUrl=Yii::$app->urlManager->createAbsoluteUrl(['main/confirm','token' => $user->token]);

    Yii::$app->mailer->compose('confirm',['confirmUrl' => $confirmUrl])
    ->setFrom(Yii::$app->params['senderEmail'])
    ->setTo($user->email)
    ->setSubject('Подтверждение почты от '.$_SERVER['SERVER_NAME'])
    ->send();

}

public function confirmEmail($token)
{
  if(empty($token)){
      throw new \DomainException('Empty token');
  }

  $user=User::findOne(['token' => $token]);
  if(!$user){
      throw new \DomainException('User is not found');
  }

  $user->token=null;
  $user->status=User::STATUS_ACTIVE;

  if(!$user->save()){
      throw new \RuntimeException('Saving error');
  }

  if(!Yii::$app->user->login($user)){
      throw new \RuntimeException('Authentication error');
  }

}


}


?>