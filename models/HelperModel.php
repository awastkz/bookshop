<?php

namespace app\models;

use yii\data\Pagination;

class HelperModel
{
    
   public static function getPagePagination($query,$page_size)
   {
      return new Pagination(['totalCount' => $query->count(),'pageSize' => $page_size,'forcePageParam' => false,'pageSizeParam' => false]);
   }

   public static function getDataPagination($query,$page_size,$sort=['id' => SORT_ASC])
   {
      $pages=self::getPagePagination($query,$page_size);
      $sort=ProductSort::sortProduct($sort);
      
      return $query->offset($pages->offset)->limit($pages->limit)->orderBy($sort)->all();
   }

}

?>