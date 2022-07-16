<?php

namespace app\components;

use yii\base\Widget;
use app\models\Category;
use app\models\Product;

class sidebarWidget extends Widget
{

    public $tpl;
    public $ul_class;
    public $data;
    public $tree;
    public $menuHtml;

public function init()
{
   parent::init();

   if($this->tpl===null) $this->tpl='menu';
   if($this->ul_class===null) $this->ul_class='menu';
   
   $this->tpl.='.php';

}

public function countCategoryProducts($category_id)
{
    return Product::find()->where(['category_id' => $category_id])->count();
}

public function run()
{

    $this->data=Category::find()->select('id,parent_id,name')->indexBy('id')->asArray()->all();
    $this->tree=$this->getTree();
    $this->menuHtml=$this->getMenuHtml($this->tree);

    return $this->menuHtml;
}

protected function getTree(){
    $tree = [];
    foreach ($this->data as $id=>&$node) {
        if (!$node['parent_id'])
            $tree[$id] = &$node;
        else
            $this->data[$node['parent_id']]['children'][$node['id']] = &$node;
    }
    return $tree;
}


public function getMenuHtml($tree)
{
    foreach($tree as $category){
        $str.=$this->catToTemplate($category);
    }
    return $str;
}

protected function catToTemplate($category)
{
 ob_start();
 include __DIR__.'/menu_tpl/'.$this->tpl;
 return ob_get_clean();
}


}



?>