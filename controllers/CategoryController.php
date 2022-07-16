<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Category;
use app\models\HelperModel;
use app\models\Product;
use app\models\Favorites;
use app\models\ProductFilter;
use \yii\helpers\Url;
use app\helpers\Helper;


class CategoryController extends BaseController
{

    public function actionView($id)
    {
        $favorites_model=new Favorites;
        $productFilter_model=new ProductFilter;
        $category=Category::findOne($id);

        if($category===null){
            throw new \yii\web\NotFoundHttpException('Такой категорий не существует');
        }

        $session=Yii::$app->session;
        $session->open();

        if(!Yii::$app->request->get('filter')){
            $productFilter_model->removeFilterProductSession($session);
        }

        $sort=Yii::$app->request->get('sort') ? Yii::$app->request->get('sort') : null;
        $query=Product::find()->where(['category_id' => $id]);

        if($productFilter_model->load(Yii::$app->request->post()) && $productFilter_model->validate()){
            try{
                $productFilter_model->setFilterProductSession($session);
                return $this->redirect(Url::current(['filter' => 'true']));
            }
            catch(\DomainException $e){
                $productFilter_model->removeFilterProductSession($session);
                Yii::$app->session->setFlash('error',$e->getMessage());
                return $this->refresh();
            }
        }
    
            if(ProductFilter::hasFilterSession($session) && Yii::$app->request->get('filter')){
                $query=$session['filter.query'];
                Yii::$app->session->setFlash('success',$productFilter_model->filterProductText($session['filter.model']));
            }

        return $this->render('view',[
            'products' => HelperModel::getDataPagination($query,Category::PAGE_SIZE,$sort ? $sort : ''),
            'category' => $category,
            'favorites_items' => $favorites_model->getFavoritesData(),
            'favorites_model' => $favorites_model,
            'pages' => HelperModel::getPagePagination($query,Category::PAGE_SIZE),
            'productFilter_model' => $productFilter_model,
            'session' => $session,
        ]);
    }

}

?>