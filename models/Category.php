<?php

namespace app\models;

use yii\db\ActiveRecord;

class Category extends ActiveRecord
{

    const PAGE_SIZE=2;

 public static function tableName()
 {
     return 'category';
 }


}

?>