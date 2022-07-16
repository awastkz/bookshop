<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimeStampBehavior;
use app\models\OrderProduct;

class Order extends ActiveRecord
{

 public static function tableName()
 {
     return 'orders';
 }

 public function behaviors()
 {
    return [
        [
            'class' => TimeStampBehavior::class,
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at','updated_at'],
                ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
            ],
            'value' => \Yii::$app->formatter->asDate('now','php:Y-m-d H:i'),
         ],
    ];
 }

 public function rules()
 {
     return [
         [['name','email'],'validateIsGuest','skipOnEmpty' => false],
         [['address','phone'],'required'],
         ['email','email'],
         ['note','safe'],
     ];
 }

 public function attributeLabels()
 {

 return [
   'name' => 'Имя',
   'email' => 'E-mail',
   'address' => 'Адрес',
   'phone' => 'Телефон',
   'note' => 'Примечание',
 ];

 }

 public function getOrderProduct()
 {
     return $this->hasMany(OrderProduct::class,['order_id' => 'id']);
 }



 public function validateIsGuest($attribute,$params)
 {
   if(\Yii::$app->user->isGuest){
     if(empty(trim($this->$attribute))) $this->addError($attribute,"Заполните поле: {$this->getAttributeLabel($attribute)}");
     }
 }


 public function saveCustomerOrder($session)
 {
    $this->user_id=Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
    $this->name=Yii::$app->user->isGuest ? $this->name : Yii::$app->user->identity->fio;
    $this->email=Yii::$app->user->isGuest ? $this->email : Yii::$app->user->identity->email;
    $this->qty=$session['cart.qty'];
    $this->sum=$session['cart.sum'];
    $this->order_number=++$this->getLastOrderNumber()[0];
    return $this->save();
 }

 public function getLastOrderNumber()
 {
     return \Yii::$app->db->createCommand('SELECT last_order_number FROM last_order')->queryColumn();
 }

 public function setLastOrderNumber()
 {
     return \Yii::$app->db->createCommand()->update('last_order',['last_order_number' => $this->order_number],'id=1')->execute();
 }




}

?>