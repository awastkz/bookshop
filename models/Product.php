<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\SaleProduct;
use app\models\Category;
use app\models\Credit;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Linkable;
use yii\web\Link;

class Product extends ActiveRecord implements Linkable
{
    const PAGE_SIZE=10;

 public static function tableName()
 {
     return 'product';
 }

 public function rules()
 {
     return [
         ['id','integer'],
         [['category_id','credit_id','picture','name','author','price','old_price','description','views','is_discount',
             'year_publish','created_at','updated_at'],'safe'],
     ];
 }


 public function getCategory()
 {
     return $this->hasOne(Category::class,['id' => 'category_id']);
 }

 public function getCredit()
 {
     return $this->hasOne(Credit::class,['id' => 'credit_id']);
 }

 public function creditItems()
 {
     return ArrayHelper::map(Credit::find()->asArray()->all(),'id','title');
 }

 public function isProductCredit($credit_id)
 {
     return $credit_id ? true : false;
 }

 public function creditText($credit_items,$credit_id)
 {
     if(array_key_exists($credit_id,$credit_items)) return $credit_items[$credit_id];
 }

 public function isProductDiscount($discount_id)
 {
    return $discount_id ? true : false;
 }

 public function discountText($old_price,$price)
 {
     return floor(100-(($price/$old_price)*100));
 }

 public function getDiscountProducts()
 {
     return self::find()->with(['category','credit'])->orderBy(['(old_price-price)' => SORT_DESC])->limit(10)->all();
 }

 public function getSaleProducts()
 {
     return ArrayHelper::getColumn(SaleProduct::find()->with(['product.category','product.credit'])->limit(10)->all(),'product');
 }


 public function fields()
 {
         $fields=parent::fields();
         unset($fields['description'],$fields['views']);

         return $fields;
 }

    public function extraFields()
    {
        return [
            'category',
            'credit',
        ];
    }

    public function getLinks()
 {
     return [
         Link::REL_SELF => Url::to(['product/view','id' => $this->id],true),
     ];
 }

}

?>