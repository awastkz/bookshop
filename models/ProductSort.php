<?php

namespace app\models;

use Yii;
use app\models\Product;
use app\models\HelperModel;

class ProductSort
{

    CONST SORT_DEFAULT='default';
    CONST SORT_NOVELTY='novelty';
    CONST SORT_INEXPENSIVE='inexpensive';
    CONST SORT_EXPENSIVE='expensive';

    CONST SORT_DEFAULT_TITLE='Default';
    CONST SORT_NOVELTY_TITLE='Novelty';
    CONST SORT_INEXPENSIVE_TITLE='Inexpensive';
    CONST SORT_EXPENSIVE_TITLE='Expensive';

    public static function sortProduct($type_sort){
        if($type_sort==self::SORT_DEFAULT) return ['id' => SORT_ASC];
        if($type_sort==self::SORT_NOVELTY) return ['created_at' => SORT_DESC];
        if($type_sort==self::SORT_INEXPENSIVE) return ['price' => SORT_ASC];
        if($type_sort==self::SORT_EXPENSIVE) return ['price' => SORT_DESC];
     
        return ['id' => SORT_ASC];
      }

      public static function productSortList()
      {
        return [
          ['sort' => self::SORT_DEFAULT,'title' => self::SORT_DEFAULT_TITLE],
          ['sort' => self::SORT_NOVELTY,'title' => self::SORT_NOVELTY_TITLE],
          ['sort' => self::SORT_INEXPENSIVE,'title' => self::SORT_INEXPENSIVE_TITLE],
          ['sort' => self::SORT_EXPENSIVE,'title' => self::SORT_EXPENSIVE_TITLE],
        ];
      }
     
      public static function isActiveSort($item_sort,$param_sort)
      {
        return $param_sort!==null && $item_sort==$param_sort ? 'active' : '';
      }
     
      public static function isExistSort($sort)
      {
        return $sort!==null ? true : false;
      }
     
      public static function getSortText($sort)
      {
        foreach(self::productSortList() as $item){
          if($item['sort']==$sort) return $item['title'];
        }
      }

      public static function allowSortUrl($controllerId,$actionId)
      {
        $allowSortUrl=['main/index','category/view'];

        foreach($allowSortUrl as $url){
          if($url==$controllerId.'/'.$actionId) return true;
        }
        return false;
      }
}

?>