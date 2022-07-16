<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\User;
use app\models\UserProfile;
use app\models\UserPurchase;
use app\models\Order;
use app\models\HelperModel;
use Yii;
use yii\web\NotFoundHttpException;

class UserController extends BaseController
{

    public function actionView($id)
    {
        $user=User::findOne($id);

        if($user===null){
            throw new NotFoundHttpException('Такой пользователь не найден');
        }

        $user_profile=new UserProfile;
        $userInfo=$user_profile->getUserInfo($user);

        return $this->render('view',[
            'user' => $user,
            'userInfo' => $userInfo,
            'user_profile' => $user_profile,
        ]);
    }

    public function actionPurchase()
    {
        $user_purchase=new UserPurchase;
        $session=Yii::$app->session;
        $session->open();

        $query=$user_purchase->purchaseItemsQuery();

        if($user_purchase->load(Yii::$app->request->post()) && $user_purchase->validate()){
            try{
                $user_purchase->getPurchaseData($session);
            }
            catch(\DomainException $e){
                $user_purchase->removeFilterSession($session);
                Yii::$app->session->setFlash('error',$e->getMessage());
            }
            
            return $this->refresh();
        }

        if(UserPurchase::hasFilterSession($session)){
            $query=$session['purchase.query'];
            Yii::$app->session->setFlash('success',$session['purchase.filter_text']);
        }


        $purchase=!Yii::$app->session->hasFlash('error') ? HelperModel::getDataPagination($query,UserPurchase::PAGE_SIZE) : null;

        return $this->render('purchase',[
            'purchase' => $purchase,
            'model' => $user_purchase,
            'pages' => HelperModel::getPagePagination($query,UserPurchase::PAGE_SIZE),
            'item_counter' => $user_purchase->itemCounter(Yii::$app->request->get('page'),UserPurchase::PAGE_SIZE),
        ]);
    }

    public function actionResetFilter()
    {
        $user_purchase=new UserPurchase;

        $session=Yii::$app->session;
        $session->open();

        if(UserPurchase::hasFilterSession($session)){
            $user_purchase->removeFilterSession($session);
        }

        return $this->redirect(Yii::$app->request->referrer);

    }

    public function actionShow()
    {
        $user_purchase=new UserPurchase;

        if(Yii::$app->request->isAjax){
            $order_id=Yii::$app->request->get('order_id');

            return $this->renderPartial('detail-modal',[
                'detail_items' => $user_purchase->detailItems($order_id),
                'user_purchase' => $user_purchase,
                'order_id' => $order_id,
            ]);
        }
    }

    public function actionSubscribe($id)
    {
        $userProfile=new UserProfile;

        if($userProfile->subscribe($id)){
            return $this->redirect(Yii::$app->request->referrer);
        }
        else Yii::$app->session->setFlash('error','Произошла ошибка повторите позже');
    }

    public function actionUnsubscribe($id)
    {
        $userProfile=new UserProfile;

        if($userProfile->unsubscribe($id)){
            return $this->redirect(Yii::$app->request->referrer);
        }
        else Yii::$app->session->setFlash('error','Произошла ошибка повторите позже');
    }

    public function actionShowModalUsers()
    {
        $userProfile=new UserProfile;

        if(Yii::$app->request->isAjax){

            $user=Yii::$app->request->get();
            $modal_users=$userProfile->getModalUsersData($user);

        return $this->renderPartial('users-modal',[
            'modal_users' => $modal_users,
        ]);

        }
    }

}

?>