<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimeStampBehavior;
use app\models\User;
use app\models\HelperModel;

class Comment extends ActiveRecord
{

 public $type;

 const PAGE_SIZE=2;

 public static function tableName()
 {
     return 'comment';
 }

 public function behaviors()
 {
   return [
       [
           'class' => TimeStampBehavior::class,
           'attributes' => [
               ActiveRecord::EVENT_BEFORE_INSERT => ['created_at','updated_at'],
           ],
           'value' => Yii::$app->formatter->asDate('now','php:d-m-Y H:i'),
        ],
   ];
 }

 public function rules()
 {
     return [
         [['id','type'],'safe'],
         ['text','required','message' => 'Нельзя отправить пустой комментарий'],
         ['text','string','max' => 50],
         ['likes','default','value' => 0],
         [['created_at','updated_at'],'safe'],
     ];
 }

 public function getUser()
 {
     return $this->hasOne(User::class,['id' => 'user_id']);
 }
 
 public function convertTime($date)
 {
    $time=strtotime($date);
    return date('j M Y | H:i',$time);
 }

 public function addComment($product_id,$text)
 {
    $this->user_id=Yii::$app->user->id;
    $this->product_id=$product_id;
    $this->text=$text;
    return $this->save();
 }

 public function editComment(){
     $comment=Comment::findOne($this->id);
     if($comment){
        $comment->text=$this->text;
        $comment->updated_at=date('Y-m-d H:i:s');
        return $comment->save();
     }
    return false;
 }


 public function checkUserComment($comment_id,$user_id)
{
   $comment=Comment::findOne($comment_id);
   if($comment){
       if($comment->user_id==$user_id) return true;
   }
   return false;
}

 public function getCommentsQuery($id)
 {
     return self::find()->where(['product_id' => $id]);
 }

 public function pageStatus($pages)
 {
    $page_status=($pages->totalCount%$pages->pageSize)==0 ? 'next' : 'default';
    if($pages->totalCount==0) $page_status='default';

    return $page_status;
 }

 public function sendComment($product_id,$pages)
 {
     if($this->validate()){
         if($this->type=='edit_comment'){
             if(!$this->editComment()){
                 throw new \RuntimeException('Произошла ошибка сообщение не редактировалась');
             }
             return ['status' => 'ok','page_status' => 'default'];
         }
         if($this->type=='add_comment'){
             if(!$this->addComment($product_id,$this->text)){
                 throw new \RuntimeException('Произошла ошибка сообщение не отправилась');
             }
             return ['status' => 'ok', 'page_status' => $this->pageStatus($pages)];
         }
     }
 }


 public function commentLike($data)
 {
     if(!Yii::$app->user->isGuest){
         $data=json_decode($data['info']);
         if($data && $data->type=='add_like'){
             if($this->addLikeComment($data)) return json_encode($data,JSON_UNESCAPED_UNICODE);
         }
         if($data && $data->type=='remove_like'){
             if($this->removeLikeComment($data)) return json_encode($data,JSON_UNESCAPED_UNICODE);
         }
     }
     return false;
 }


 public function addLikeComment($data)
 {
    $comment=Comment::findOne($data->comment_id);

    if($comment && $data->user_id!=''){
       return Yii::$app->redis->sadd("comment_id:{$data->comment_id}:likes",$data->user_id);
    }

    return false;
 }

 public function removeLikeComment($data)
 {
    $comment=Comment::findOne($data->comment_id);

    if($comment && $data->user_id!=''){
        return Yii::$app->redis->srem("comment_id:{$data->comment_id}:likes",$data->user_id);
    }

    return false;
 }

 public function checkUserLike($comment_id,$user_id)
 {
    return Yii::$app->redis->sismember("comment_id:{$comment_id}:likes",$user_id);
 }
 

 public function countLikeComment($comment_id,$user_id)
 {
     return Yii::$app->redis->scard("comment_id:{$comment_id}:likes");
 }

}

?>