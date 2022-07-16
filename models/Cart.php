<?php

namespace app\models;

use Yii;
use app\models\Order;
use app\models\OrderProduct;

class Cart
{

    public function userCheckout(Order $order,$session)
    {
        $order_product=new OrderProduct;

        if($session['cart']===null){
            throw new \DomainException('Корзина пуста');
        }

        $transaction=Yii::$app->getDb()->beginTransaction();

        if($order->saveCustomerOrder($session) && $order->setLastOrderNumber()
            && $order_product->saveOrderProduct($order->id,$session)){
                $transaction->commit();
                $session->remove('cart');
                $session->remove('cart.qty');
                $session->remove('cart.sum');
               }

        else{
            $transaction->rollBack();
            throw new \RuntimeException('Транзакция не выполнилось повторите позже');
        }

        
    }

 public function addToCart($session,$product)
 {

    $_SESSION['cart'][$product->id]=[
        'id' => $product->id,
        'picture' => $product->picture,
        'name' => $product->name,
        'author' => $product->author,
        'qty' => $_SESSION['cart'][$product->id]['qty'] ? $_SESSION['cart'][$product->id]['qty']+1 : 1,
        'old_price' => $product->old_price,
        'price' => $product->price,
    ];
    
    if($this->checkLimitCart($product->id)){
        $_SESSION['cart.qty']=$_SESSION['cart.qty'] ? $_SESSION['cart.qty']+1 : 1;
        $_SESSION['cart.sum']=$_SESSION['cart.sum'] ? $_SESSION['cart.sum']+$_SESSION['cart'][$product->id]['price'] : $_SESSION['cart'][$product->id]['price'];    
    }

 }

 public function calculateCart($data,$session)
 {
      if($_SESSION['cart'][$data->product_id]){
          if($data->calc=='plus'){
              $_SESSION['cart'][$data->product_id]['qty']++;
              if($this->checkLimitCart($data->product_id)){
                $_SESSION['cart.sum']+=$_SESSION['cart'][$data->product_id]['price'];
                $_SESSION['cart.qty']++;
              }
          }
          if($data->calc=='minus'){
              if($_SESSION['cart'][$data->product_id]['qty']>1){
                $_SESSION['cart'][$data->product_id]['qty']--;
                $_SESSION['cart.qty']--;
                $_SESSION['cart.sum']-=$_SESSION['cart'][$data->product_id]['price'];
              }
          }
      }
 }



 public function checkLimitCart($product_id){
     if($_SESSION['cart'][$product_id]){
        if($_SESSION['cart'][$product_id]['qty']>10){
            $_SESSION['cart'][$product_id]['qty']=10;
            return false;
        }
     }
     return true;  
 }
 
 public function removeItem($id)
 {
    if($_SESSION['cart'][$id]){
        $_SESSION['cart.qty']-=$_SESSION['cart'][$id]['qty'];
        $_SESSION['cart.sum']-=($_SESSION['cart'][$id]['qty']*$_SESSION['cart'][$id]['price']);
        unset($_SESSION['cart'][$id]);
    }
 }

}

?>