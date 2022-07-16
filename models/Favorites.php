<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Category;
use yii\data\Pagination;

class Favorites
{
    
 private $cookieDataGuest=[];
 private $categoriesItems=[];

 const PAGE_SIZE=1;


 public function setFavorites($favorites)
 {
   $cookies=Yii::$app->request->cookies;
   $new_cookie=Yii::$app->response->cookies;

   $product_id=$favorites['product_id'];
   $type=$favorites['type'];

    if($type=='add_favorites'){
       if(Yii::$app->user->isGuest){
          if(!$cookies->has('favorites')){
             return $this->addFavoritesGuest($new_cookie,$product_id);
          }
          else{
            $new_data=$cookies->getValue('favorites');
            $new_data[]=$product_id;
            return $this->addFavoritesGuest($new_cookie,$new_data,$cookies,$product_id);
          }
          return false;
       }

       if(!Yii::$app->user->isGuest){
          return $this->addFavoritesUser(Yii::$app->user->id,$product_id);
       }
    }

    if($type=='remove_favorites'){
      if(Yii::$app->user->isGuest){
         return $this->removeFavoritesGuest($cookies,$product_id);
      }
      else return $this->removeFavoritesUser(Yii::$app->user->id,$product_id);
    }

 }

 public function removeFavorites($favorites)
 {
    return Yii::$app->user->isGuest ? $this->removeFavoritesGuest(Yii::$app->request->cookies,$favorites['product_id'])
                           : $this->removeFavoritesUser(Yii::$app->user->id,$favorites['product_id']);
 }


public function getCategoriesList()
{
   return Category::find()->indexBy('id')->asArray()->all();
}

public function pagePagination($query)
{
   return new Pagination(['totalCount' => $query->count(),'pageSize' => self::PAGE_SIZE,'forcePageParam' => false,'pageSizeParam' => false]);
}

public function dataPagination($query,$page_pagination=null)
{
   $pages=$this->pagePagination($query);

   if($page_pagination!==null) $pages->page=$page_pagination;

   return $query->offset($pages->offset)->limit($pages->limit)->all();
}

public function statusPage($products,$currentPage=null,$pages)
{
   if($currentPage===null) return null;
   if(count($products)==0 && $currentPage>$pages->pageCount) $page_favorites='prev';
   if((count($products)==0 && $currentPage=='') || (count($products)==0 && $currentPage==1)) $page_favorites='current';

   return $page_favorites;
}


 public function addFavoritesGuest($new_cookie,$new_data,$cookies=null,$product_id=null)
 {
  if(!$this->isRepeatFavoritesGuest($cookies,$product_id)){
      
     if(!empty($new_data)){
        if(gettype($new_data)=='string') $new_data=[$new_data];
         $this->createFavoritesGuest($new_cookie,$new_data);
         return true;
  }
}
    return false;
 }

 public function isRepeatFavoritesGuest($cookies,$product_id)
 {
   if($cookies!==null && $product_id!==null){
     if($cookies->has('favorites')){
         $cookies_value=$cookies->getValue('favorites');
          if(in_array($product_id,$cookies_value)) return true;
      }
    }
     return false;
 }

 public function removeFavoritesGuest($cookies,$product_id)
 {
   if($this->isRepeatFavoritesGuest($cookies,$product_id)){
    $new_data=array_diff($cookies->getValue('favorites'),[$product_id]);
    $new_cookie=Yii::$app->response->cookies;
    $flag=false;

      if(count($new_data)>0){ $this->createFavoritesGuest($new_cookie,$new_data); $flag=true; }
      else $new_cookie->remove('favorites');

      if($flag) $this->setFavoritesDataGuest($new_data,$cookies);

      return true;
   }

   return false;
 }

 public function setFavoritesDataGuest($new_data,$cookies)
 {
    if(!empty($new_data)) $this->cookieDataGuest=$new_data;
    else $this->cookieDataGuest=$cookies->get('favorites') ? $cookies->getValue('favorites') : '';
 }

 public function getFavoritesDataGuest()
 {
    return $this->cookieDataGuest;
 }

 public function createFavoritesGuest($new_cookie,$new_data)
 {
    $new_cookie->add(new \yii\web\Cookie([
        'name' => 'favorites',
        'value' => $new_data,
        'expire' => time()+3600*24,
             ]));
 }

 
 public function addFavoritesUser($user_id,$product_id)
 {
     if(!$this->isRepeatFavoritesUser($user_id,$product_id)){
       \Yii::$app->db->createCommand()->insert('favorites',[
        'user_id' => $user_id,
        'product_id' => $product_id,
       ])->execute();
     return true;
     }
     return false;
 }


 public function isRepeatFavoritesUser($user_id,$product_id)
 {
     $products=[];

     $products=\Yii::$app->db->createCommand('SELECT product_id FROM favorites WHERE user_id=:user_id')
     ->bindValue(':user_id',$user_id)
     ->queryAll();
     
     if(!empty($products)){
        $products=ArrayHelper::getColumn($products,'product_id');
        if(in_array($product_id,$products)) return true;
     }

     return false;
 }

 public function removeFavoritesUser($user_id,$product_id)
 {
     if($this->isRepeatFavoritesUser($user_id,$product_id)){
        return \Yii::$app->db->createCommand()->delete('favorites','product_id='.$product_id)->execute();
     }
     return false;
 }


 public function getFavoritesData()
 {
    $data=[];
    $cookies=Yii::$app->request->cookies;

     if(Yii::$app->user->isGuest){
        if($cookies->has('favorites')) $data=$cookies->getValue('favorites');
     }
     
     else{
        $user_id=Yii::$app->user->id;
        $data=Yii::$app->db->createCommand('SELECT product_id FROM favorites WHERE user_id=:user_id')
        ->bindValue(':user_id',$user_id)
        ->queryAll();
        if(!empty($data)) $data=ArrayHelper::getColumn($data,'product_id');
     }

     return $data;
 }

 public function checkFavorites($product_id,$data)
 {
     if(!empty($data)){
        if(in_array($product_id,$data)) return true;
     }
     
     return false;
 }

 public function getCategoriesItems($category_id,$categories)
 {
    $categories_id=ArrayHelper::getColumn($categories,'id');
    if(in_array($category_id,$categories_id)){
        array_unshift($this->categoriesItems,$categories[$category_id]['name']);
        if($categories[$category_id]['parent_id']!=0){
            $this->getCategoriesItems($categories[$category_id]['parent_id'],$categories);
        }
    }
    
    return $this->dataCategoriesItems();
 }

 public function dataCategoriesItems()
 {
     $result='';
     
     if(!empty($this->categoriesItems)){
         foreach($this->categoriesItems as $item){
            $result.=$item.'/';
         }
     }

     return mb_substr($result,0,mb_strlen($result)-1);
     
 }

public function clearCategoriesItems()
{
   $this->categoriesItems=[];
}


}

?>