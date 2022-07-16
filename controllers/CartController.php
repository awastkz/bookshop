<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Product;
use app\models\Comment;
use app\models\Order;
use app\models\OrderProduct;
use app\models\User;
use app\models\Cart;
use yii\web\Response;
use yii\widgets\ActiveForm;

class CartController extends BaseController
{

    public function actionAdd($product_id){
        $product=Product::findOne($product_id);
        $cart=new Cart;
        $session=Yii::$app->session;
        $session->open();

        if($product){
           $cart->addToCart($session,$product);
           if(Yii::$app->request->isAjax){
               return $this->renderPartial('modal-cart',compact('session'));
           }
        }

    }

    public function actionShow()
    {
        $session=Yii::$app->session;
        $session->open();
         if(Yii::$app->request->isAjax){
            return $this->renderPartial('modal-cart',compact('session'));
         }
       
    }

    public function actionClear()
    {
        $session=Yii::$app->session;
        $session->remove('cart');
        $session->remove('cart.qty');
        $session->remove('cart.sum');

        if(Yii::$app->request->isAjax){
            return $this->renderPartial('modal-cart',compact('session'));
         }
    }


    public function actionCheckout()
    {
        $order=new Order;
        $cart=new Cart;

        $session=Yii::$app->session;
        $session->open();

        if($order->load(Yii::$app->request->post()) && $order->validate()){
            try{
                $cart->userCheckout($order,$session);
                Yii::$app->session->setFlash('success','Транзакция успешно выполнено');
                return $this->refresh();
            }

            catch(\Exception $e){
                Yii::$app->session->setFlash('error',$e->getMessage());
            }
        }

        return $this->render('checkout',compact('session','order'));
    }


    public function actionCalc(){
        $session=Yii::$app->session;
        $session->open();
        
        if(Yii::$app->request->isAjax){
            $data=Yii::$app->request->get();
            $data=json_decode($data['info']);
    
            if($data){
                $cart=new Cart;
                $cart->calculateCart($data,$session);
                return $this->renderPartial('checkout-cart',compact('session'));
            }
        }

    }
    


    public function actionRemove()
    {
        $session=Yii::$app->session;
        $session->open();
        $cart=new Cart;
        
        if(Yii::$app->request->isAjax){
            $id=Yii::$app->request->get('id');
            $type=Yii::$app->request->get('type');

            if($id && $type=='checkout'){
                $cart->removeItem($id);
                return $this->renderPartial('checkout-cart',compact('session'));
            }

            if($id && $type=='modal'){
                $cart->removeItem($id);
                return $this->renderPartial('modal-cart',compact('session'));
            }
        }
        
    }




}




?>