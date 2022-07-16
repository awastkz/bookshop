<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Product;


class OrderProduct extends ActiveRecord
{

 public static function tableName()
 {
     return 'order_product';
 }

 public function getProduct()
 {
     return $this->hasOne(Product::class,['id' => 'product_id']);
 }

public function saveOrderProduct($order_id,$session)
{
     foreach($session['cart'] as $cart => $product){
         $this->id=null;
         $this->isNewRecord=true;
         $this->order_id=$order_id;
         $this->product_id=$product['id'];
         $this->name=$product['name'];
         $this->author=$product['author'];
         $this->qty=$product['qty'];
         $this->old_price=$product['old_price'];
         $this->price=$product['price'];
        
         if(!$this->save()) return false;
     }

     return true;
}


}

?>