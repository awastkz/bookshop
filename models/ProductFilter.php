<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Category;
use app\models\HelperModel;
use app\models\Product;

use yii\helpers\ArrayHelper;

class ProductFilter extends ActiveRecord
{

    public $category_id;
    public $price_from;
    public $price_to;
    public $year_publish_from;
    public $year_publish_to;
    public $discount;
    public $credit;

    private $categoryItems=[];

    public function rules()
    {
        return [
            [['category_id','price_from','price_to','year_publish_from','year_publish_to','discount','credit'],'safe'],
            ['price_from','priceFromCompareValidate'],
            ['year_publish_from','compare','compareAttribute' => 'year_publish_to','operator' => '<=','type' => 'number'],
        ];
    }

    public function priceFromCompareValidate($attributes,$params){
        if($this->price_from!='' && $this->price_to!='' && $this->price_from>$this->price_to){
            $this->addError($attributes,'Начальная цена не должна превышать последнию цену');
        }
    }

    public function setFilterProductSession($session)
    {
        $query=$this->filterProductQuery();
        $products=HelperModel::getDataPagination($query,Product::PAGE_SIZE);

        if(count($products)==0){
            throw new \DomainException('К сожелению по фильтраций такие продукты не найдены');
        }

        $session['filter.query']=$query;
        $session['filter.model']=$this;
    }

    public function categoryItems()
    {
        $categories=Category::find()->indexBy('id')->asArray()->all();
        return ArrayHelper::map($this->sortCategoryItems($categories),'id','name');
    }

    public function sortCategoryItems($categories)
    {
        foreach($categories as $index => $category){
            if($category['parent_id']!=0){
                $categories[$category['parent_id']]['children'][$category['id']]=[
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'parent_id' => $category['parent_id'],
                ];
            }
        }
        return $this->resultSortItems($categories);
    }

    public function resultSortItems($categories)
    {

   foreach($categories as $category){
       if($category['children']===null){
         if($category['parent_id']!=0) $category['name']='--'.$category['name'];
           $this->categoryItems[]=$category;
       }
       else{
        $this->categoryItems[]=$category;
        $this->resultSortItems($category['children']);
       }
   }
   return $this->categoryItems;
}

public function discountItems()
{
    $discount=[
        0 => 'Без скидки',
        1 => 'Со скидкой',
        '' => 'Все',
    ];

    return $discount;
}

public function creditItems()
{
    $credits=ArrayHelper::map(\Yii::$app->db->createCommand('SELECT * FROM credits')->queryAll(),'id','title')+['' => 'Все'];

    return $credits;
}

public function minYearPublish()
{
    return \Yii::$app->db->createCommand('SELECT MIN(year_publish) FROM product')->queryScalar();
}

public function maxYearPublish()
{
    return \Yii::$app->db->createCommand('SELECT MAX(year_publish) FROM product')->queryScalar();
}

public function yearRange()
{
    $year_values=array_values(range($this->minYearPublish(),$this->maxYearPublish()));

    return array_combine($year_values,$year_values);
}

public function minPrice()
{
    return \Yii::$app->db->createCommand('SELECT MIN(price) FROM product')->queryScalar();
}

public function maxPrice()
{
    return \Yii::$app->db->createCommand('SELECT MAX(price) FROM product')->queryScalar();
}

public static function hasFilterSession($session)
{
    return $session->has('filter.query') && $session->has('filter.model');
}

public function removeFilterProductSession($session){
    if(self::hasFilterSession($session)){
        $session->remove('filter.query');
        $session->remove('filter.model');
    }
}

public function filterProductQuery()
{

    if(empty($this->price_from)) $this->price_from=$this->minPrice();
    if(empty($this->price_to)) $this->price_to=$this->maxPrice();

               $query=Product::find()
               ->filterWhere(['category_id' => $this->category_id])
               ->andFilterWhere(['between','price',$this->price_from,$this->price_to])
               ->andFilterWhere(['between','year_publish',$this->year_publish_from,$this->year_publish_to])
               ->andFilterWhere(['is_discount' => $this->discount])
               ->andFilterWhere(['credit_id' => $this->credit]);

    return $query;

}

public function filterProductCategoryName($category_id)
{
    return $category_id ? ltrim($this->categoryItems()[$category_id],'-') : 'Все';
}

public function filterProductCreditName($credit_id)
{
     return $credit_id ? 'В кредит '.$this->creditItems()[$credit_id] : '';
}

public function filterProductDiscountName($discount_id)
{
    return $discount_id ? $this->discountItems()[$discount_id] : '';
}

public function filterProductText($model)
{
    return 'Фильтрация книг<br>Категория: '.$model->filterProductCategoryName($model->category_id).'<br>Цена: от
         '.$model->price_from.' до '.$model->price_to.'
         <br>Год издательство от '.$model->year_publish_from.' до '.$model->year_publish_to.'<br>
         '.$model->filterProductDiscountName($model->discount).' '.$model->filterProductCreditName($model->credit);
}



}

?>