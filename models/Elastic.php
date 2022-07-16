<?php

namespace app\models;
use yii\elasticsearch\ActiveRecord;
use app\models;
use yii\helpers\ArrayHelper;

class Elastic extends ActiveRecord
{

    public static function index()
    {
        return 'shop';
    }

    public static function type()
    {
        return 'products';
    }
    
    public function attributes()
    {
        return [
            'title',
            'description',
        ];
    }

    public function rules()
    {
        return [
            [['title','description'],'safe']
        ];
    }

    public static function mapping()
    {
        return [
            'title' => ['type' => 'text'],
            'description' => ['type' => 'text'],
        ];
    }

    public static function createIndex()
    {
        return static::getDb()->createCommand()->createIndex(static::index(),[
            'mappings' => static::mapping(),
        ]);
    }

    public static function deleteIndex()
    {
        return static::getDb()->createCommand()->deleteIndex(static::index(),static::type());
    }

    public function searchSuggest($keyword)
    {
        $params=[
            'text' => $keyword,
            'term' => [
                'field' => 'title',
            ],
        ];

        $data=$this->find()->limit(0)->addSuggester('book_name',$params)->search();
        $data=ArrayHelper::map($data['suggest']['book_name'],'text','options');

        return ArrayHelper::getColumn($data[$keyword],'text');
    }
}

?>