<?php

namespace app\models;

use yii\db\ActiveRecord;

class SaleProduct extends ActiveRecord
{

    public static function tableName()
    {
        return 'sale_products';
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class,['id' => 'product_id']);
    }
    

}

?>