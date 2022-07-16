<?php

namespace app\models;

use app\api\modules\v1\models\Token;
use yii\base\Model;
use app\models\User;
use Yii;
use yii\widgets\ActiveForm;
use yii\web\Response;


class Login extends Model
{

    public $login_email;
    public $login_password;
    public $rememberMe=true;

  public function rules()
  {
      return [
       [['login_email','login_password'],'required'],
       ['login_email','email'],
       ['rememberMe','boolean'],
       ['login_password','validatePassword'], 
      ];
  }

  public function attributeLabels()
  {
    return [
      'login_email' => 'E-mail',
      'login_password' => 'Пароль',
    ];
  }

  public function validatePassword($attribute,$params)
  {
      if(!$this->hasErrors()){
        $user=$this->getUser();
        if(!$user || !$user->validateHashPassword($this->login_password)){
        $this->addError($attribute,'Не верны логин или пароль');
       }
    }
}


  public function getUser()
  {
      return User::findOne(['email' => $this->login_email]);
  }

  public function userLogin()
  {
      $user=$this->getUser();
      if($user->status==User::STATUS_ACTIVE){
        if($this->rememberMe){ $user->generateAuthKey(); $user->save(); }
        Yii::$app->user->login($user,$this->rememberMe ? 3600*24*30 : 0);
      }
      if($user->status==User::STATUS_INACTIVE) throw new \DomainException('Емайл не подтвержден');
  }

  public function auth()
  {
      if($this->validate()){
          $token=new Token;
          $token->user_id=$this->getUser()->id;
          $token->generateToken(time()+3600*24);
          return $token->save() ? $token : null;
      }
      else return null;
  }

}

?>