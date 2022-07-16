<?php

namespace app\models;

use app\api\modules\v1\models\Token;
use yii\db\ActiveRecord;
use Yii;
use app\modules\admin\models\Ban;
use app\modules\admin\models\UserBan;

class User extends ActiveRecord implements \yii\web\IdentityInterface  
{

    const STATUS_INACTIVE=0;
    const STATUS_ACTIVE=1;
    const STATUS_BANNED=2;

    const ROLE_USER='user';
    const ROLE_MANAGER='manager';
    const ROLE_MODERATOR='moderator';
    const ROLE_ADMIN='admin';

    public function rules()
    {
        return [
            [['fio','email'],'required'],
            ['email','email'],
        ];
    }

    public static function tableName()
    {
        return 'users';
    }

    public function getBan()
    {
        return $this->hasOne(UserBan::class,['user_id' => 'id']);
    }

    public function setHashPassword($password)
    {
        $this->password=Yii::$app->getSecurity()->generatePasswordHash($password);
    }


    public function validateHashPassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password,$this->password);
    }

    public function confirmEmail($token)
    {
         $user=User::find()->where(['token' => $token]);
    }

    public function generateAuthKey()
    {
        $this->auth_key=Yii::$app->getSecurity()->generateRandomString();
    }

    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public function getId()
    {
        return $this->id;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->joinWith('tokens t')
            ->andWhere(['t.token' => $token])
            ->andWhere(['>', 't.expired_at', time()])
            ->one();
    }

    public function getAuthKey()
    {
       return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
       return $this->auth_key===$authKey;
    }

    public function checkBanUsers()
    {
        $userBan=new Ban;

        return $userBan->getBanModalButtons($this);
    }


    public function getTokens()
    {
        return $this->hasMany(Token::class,['user_id' => 'id']);
    }

}

?>