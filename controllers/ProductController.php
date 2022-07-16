<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Product;
use app\models\Category;
use app\models\Comment;
use app\models\HelperModel;
use app\models\User;
use app\models\Favorites;
use app\models\UserProfile;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\Pagination;

class ProductController extends BaseController
{


public function actionView($id)
{
    $product=Product::findOne($id);
    $favorites=new Favorites;
    $model=new Comment;
    $user_profile=new UserProfile;

    if($product===null){
      throw new \yii\web\NotFoundHttpException('Такого продукта нет');
    }

    $user=User::findOne(Yii::$app->user->id);
    $product->updateCounters(['views' => 1]);
    
    $query=$model->getCommentsQuery($id);
    $pages=HelperModel::getPagePagination($query,Comment::PAGE_SIZE);
    $comments=HelperModel::getDataPagination($query,Comment::PAGE_SIZE);

    if($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax){
         Yii::$app->response->format=Response::FORMAT_JSON;
         
         try{
           return $model->sendComment($id,$pages);
         }
         catch(\RuntimException $e){
           Yii::$app->session->setFlash('error',$e->getMessage());
         }
         
      }

    return $this->render('view',[
      'product' => $product,
      'model' => $model,
      'comments' => $comments,
      'user' => $user,
      'pages' => $pages,
      'favorites_items' => $favorites->getFavoritesData(),
      'favorites' => $favorites,
      'user_profile' => $user_profile,
    ]);
}

public function actionLike()
{
    $model=new Comment;

  if(Yii::$app->request->isAjax){
     $comment=Yii::$app->request->get();
     return $model->commentLike($comment);
  }

}

public function actionFavoritesProduct()
{
  $product=new Product;
  $favorites=new Favorites;

  if(Yii::$app->request->isAjax){
    $favorites_data=Yii::$app->request->get();
    return $favorites->setFavorites($favorites_data);
  }

}


public function actionFavorites()
{
  $model=new Favorites;
  $query=Product::find()->where(['id' => $model->getFavoritesData()]);

  if(Yii::$app->request->isAjax){

    $favorites=Yii::$app->request->get();
    $page_pagination=$favorites['page'];
  
        if($model->removeFavorites($favorites)){
        
          if(Yii::$app->user->isGuest) $query=Product::find()->where(['id' => $model->getFavoritesDataGuest()]);
          else $query=Product::find()->where(['id' => $model->getFavoritesData()]);

          
          

          return $this->renderPartial('favorites-items',[
            'products' => $model->dataPagination($query,$page_pagination),
            'pages' => $model->pagePagination($query),
            'page_status' => $model->statusPage($model->dataPagination($query,$page_pagination),$page_pagination,$model->pagePagination($query)),
            'model' => $model,
          ]);
      
        }
  }

  return $this->render('favorites',[
    'products' => $model->dataPagination($query,$page_pagination),
    'pages' => $model->pagePagination($query),
    'model' => $model,
    'page_status' => null,
  ]);
}



}


?>