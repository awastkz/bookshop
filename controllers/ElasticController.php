<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Elastic;
use app\models\Product;
use yii\helpers\ArrayHelper;

class ElasticController extends BaseController
{

    public function actionIndex()
    {
        $products=Product::find()->each();
        $elastic=new Elastic;

        foreach($products as $product){
            $elastic->setPrimaryKey($product->id);
            $elastic->attributes=[
                'title' => $product->name,
                'description' => $product->description,
            ];
            $elastic->insert();
        }
    }

}


?>