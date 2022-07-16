<?php

namespace app\components;

use app\models\Auth;
use app\models\User;
use app\models\Register;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

class AuthHandler
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client=$client;
    }

    public function getUserAttributes()
    {
        return $this->client->getUserAttributes();
    }

    public function findAuth($params)
    {
        return Auth::find()->where($params)->one();
    }

    public function handle()
    {
        $attributes=$this->getUserAttributes();
        $params=[
            'source_id' => $attributes['id'],
            'source' => $this->client->getId(),
        ];

        $auth=$this->findAuth($params);
        
        if($auth){
            $user=$auth->user;
            if($user!==null) Yii::$app->user->login($user);
        }

        if($auth===null){
            $transaction=Yii::$app->getDb()->beginTransaction();

            if($this->registerUser() && $this->registerAuth()){
                $transaction->commit();
                $register_auth=$this->findAuth($params);
                $user=$register_auth->user;
                if($user!==null) Yii::$app->user->login($user);
            }

            else $transaction->rollBack();
        }
    }

    public function registerUser()
    {
        $user=new User();
        $attributes=$this->getUserAttributes();

        $user->fio=$attributes['login'];
        $user->email=$attributes['login'];
        $user->setHashPassword(Yii::$app->getSecurity()->generateRandomString(10));
        $user->avatar=Register::DEFAULT_AVATAR;
        $user->generateAuthKey();
        $user->ip=Yii::$app->request->userIp;
        $user->verified=Register::USER_VERIFIED;
        $user->token=null;
        $user->reset_token=null;
        $user->reset_time=null;
        $user->last_active=date('d-m-Y H:i');
        $user->register_date=date('d-m-Y H:i');
        return $user->save(false);
    }

    public function registerAuth()
    {
        $auth=new Auth();
        $attributes=$this->getUserAttributes();

        $user=User::findOne(['email' => $attributes['login']]);
        if(!$user) return false;

        $auth->user_id=$user->id;
        $auth->source=$this->client->getId();
        $auth->source_id=$attributes['id'];
        return $auth->save();
    }
}

?>