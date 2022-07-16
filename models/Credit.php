<?php

namespace app\models;

use yii\db\ActiveRecord;

class Credit extends ActiveRecord
{
    
    public static function tableName()
    {
        return 'credits';
    }

}


?>