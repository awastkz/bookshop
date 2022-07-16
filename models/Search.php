<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Product;
use app\models\Elastic;

class Search extends ActiveRecord
{

    public $keyword;

    public function rules()
    {
        return [
            ['keyword','trim'],
            ['keyword','required'],
            ['keyword','string','min' => 3],
        ];
    }

    public function searchItems($keyword)
    {
        $params=[
            'match' => [
                'title' => $keyword,
            ],
        ];

        $ids=[];

        $elastic=new Elastic();
        $products=$elastic->find()->query($params)->limit(10)->all();
        foreach($products as $product) $ids[]=$product->_id;

        return $ids;
    }

    public function search()
    {
        if($this->validate()){
           return Product::find()->where(['id' => $this->searchItems($this->keyword)])->all();
        }
    }
    


}


?>