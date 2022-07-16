<?php

namespace app\models;

use Yii;
use app\models\Order;
use app\models\User;
use app\models\OrderProduct;
use yii\db\ActiveRecord;

class UserProfile extends ActiveRecord
{
    public $fio;
    public $password;
    public $password_repeat;

    const USER_ONLINE=1;
    const USER_OFFLINE=0;

    const USERS_TYPE_FOLLOWERS='followers';
    const USERS_TYPE_SUBSCRIPTIONS='subscriptions';
    const USERS_TYPE_MUTUAL_FAMILIAR='mutual_familiar';

    public function rules()
    {
        return [
            [['fio','password','password_repeat'],'required'],
            [['fio','password','password_repeat'],'filter','filter' => 'trim'],
            ['password','string','min' => 6],
            ['password_repeat','compare','compareAttribute' => 'password'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fio' => 'ФИО',
            'password' => 'Новый пароль',
            'password_repeat' => 'Повторить новый пароль',
        ];
    }

    public function getUser($id)
    {
        return User::findOne($id);
    }

    public function getFio()
    {
        $user=$this->getUser(Yii::$app->user->id);

        if($user!==null) return $user->fio;

        return null;
    }

    public function getUserInfo(User $user)
    {
        $user_info=[];
        $orders=Order::find()->where(['user_id' => $user->id])->count();
        $products_count=Order::find()->where(['user_id' => $user->id])->sum('qty');
        $total_sum=Order::find()->where(['user_id' => $user->id])->sum('sum');
        $user_info=[
            'register_date' => $user->register_date,
            'status' => $this->checkUserStatus($user->last_active),
            'last_active' => $this->checkUserStatus($user->last_active) ? '' : $user->last_active,
            'email' => $user->email,
            'orders' => $orders!==null ? $orders : 0,
            'products_count' => $products_count!==null ? $products_count : 0,
            'total_sum' => $total_sum!==null ? $total_sum : 0,
        ];

        return $user_info;
    }

    public function checkUserStatus($last_active)
    {
         if(strtotime($last_active)+300>=time()) return self::USER_ONLINE;
         else return self::USER_OFFLINE;
    }

    public function editUserData()
    {
        $user=User::findOne(['id' =>Yii::$app->user->id]);
        $user->fio=$this->fio;
        $user->setHashPassword($this->password);

        if(!$user->save()){
            throw new \RuntimeException('Произошла ошибка');
        }
    }

    public function isSubscribe($user_id)
    {
        $current_user=Yii::$app->user->identity;
        return Yii::$app->redis->sismember("user:{$current_user->id}:subscriptions",$user_id);
    }

    public function subscribe($id)
    {
        $current_user=Yii::$app->user->identity;
        $redis=Yii::$app->redis;

        return $redis->sadd("user:{$current_user->id}:subscriptions",$id) &&
                         $redis->sadd("user:{$id}:followers",$current_user->id);
    }

    public function unsubscribe($id)
    {
        $current_user=Yii::$app->user->identity;
        $redis=Yii::$app->redis;

        return $redis->srem("user:{$current_user->id}:subscriptions",$id) &&
                        $redis->srem("user:{$id}:followers",$current_user->id);
    }

    public function getModalUsersData($user)
    {
        $ids=$this->getModalUsersIds($user['id'],$user['type']);

        return User::find()->select('id,fio,avatar')->where(['id' => $ids])->asArray()->all();
    }

    public function getFollowers($user_id)
    {
        return  Yii::$app->redis->smembers("user:{$user_id}:followers");
    }

    public function getSubscriptions($user_id)
    {
        return Yii::$app->redis->smembers("user:{$user_id}:subscriptions");
    }

    public function getMutualFamiliar($user_id)
    {
        $current_user=Yii::$app->user->identity;
        return Yii::$app->redis->sinter("user:{$current_user->id}:subscriptions","user:{$user_id}:followers");
    }

    public function getModalUsersIds($user_id,$users_type)
    {
        if(!empty($user_id) && !empty($users_type)){
            if($users_type==self::USERS_TYPE_FOLLOWERS) return $this->getFollowers($user_id);
            if($users_type==self::USERS_TYPE_SUBSCRIPTIONS) return $this->getSubscriptions($user_id);
            if($users_type==self::USERS_TYPE_MUTUAL_FAMILIAR) return $this->getMutualFamiliar($user_id);
        }
    }

    public function countFollowers($user_id)
    {
        return Yii::$app->redis->scard("user:{$user_id}:followers");
    }

    public function countSubscriptions($user_id)
    {
        return Yii::$app->redis->scard("user:{$user_id}:subscriptions");
    }

    public function countMutualFamiliar($user_id)
    {
        return count($this->getMutualFamiliar($user_id));
    }


    

}

?>