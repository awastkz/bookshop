<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Order;

class UserPurchase extends ActiveRecord
{
    public $date_from;
    public $date_to;
    public $date_test;

    const PAGE_SIZE=10;

    public function rules()
    {
        return [
            [['date_from','date_to'],'required'],
            ['date_from','compare','compareAttribute' => 'date_to','operator' => '<','type' => 'datetime'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'date_from' => 'От',
            'date_to' => 'До',
        ];
    }

    public function getPurchaseData($session)
    {
        $query=$this->filterQuery();
        $purchase=HelperModel::getDataPagination($query,self::PAGE_SIZE);

        if(count($purchase)==0){
            throw new \DomainException('К сожелению по фильтраций покупки не найдены');
        }
        
        $session['purchase.query']=$query;
        $session['purchase.filter_text']=$this->filterText();

    }

    public function purchaseItemsQuery()
    {
        return Order::find()->with('orderProduct')->where(['user_id' => \Yii::$app->user->id]);
    }

    public function detailItems($order_id)
    {
        return OrderProduct::find()->with('product')->where(['order_id' => $order_id])->all();
    }

    public function totalQuantity($order_id)
    {
        return OrderProduct::find()->where(['order_id' => $order_id])->sum('qty');
    }

    public function totalSum($order_id)
    {
        return Yii::$app->db->createCommand('SELECT SUM(qty*price) FROM order_product WHERE order_id=:order_id')
        ->bindValue(':order_id',$order_id)
        ->queryScalar();
    }

    public function itemCounter($page,$page_size){
        $purchaseCounter=1;
        $currentPage=$page ? $page : 1;
        if($currentPage>1) $purchaseCounter=($currentPage-1)*$page_size+($currentPage-1);

        return $purchaseCounter;
    }

    public function filterQuery()
    {
        return Order::find()->filterWhere(['between','created_at',$this->date_from,$this->date_to]);
    }

    public function filterText()
    {
        return 'Фильтр покупок с '.$this->convertDate($this->date_from).' по '.$this->convertDate($this->date_to);
    }

    public function convertDate($date)
    {
        return date('d-m-Y',strtotime($date));
    }

    public function removeFilterSession($session)
    {
       if(self::hasFilterSession($session)){
           $session->remove('purchase.query');
           $session->remove('purchase.filter_text');
       }
    }

    public static function hasFilterSession($session)
    {
        return $session->has('purchase.query') && $session->has('purchase.filter_text');
    }
    
}

?>