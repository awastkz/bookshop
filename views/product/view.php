<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title=$product->name;
$this->params['breadcrumbs'][]=['label' => $product->category->name,'url' => ['category/view','id' => $product->category->id]];
$this->params['breadcrumbs'][]=['label' => $product->name];

?>

<div>
    <div class="product-item text-center">
<h2><?= $product->name ?></h2>
<div><?= Html::img("@web/images/{$product->picture}",['width' => '40%']) ?></div>
<div><i <?= $favorites->checkFavorites($product->id,$favorites_items) ? 'class="user-favorites bi bi-shield-fill" data-favorites="1"' : '' ?> class="user-favorites bi bi-shield" data-id='<?= $product->id ?>' style='font-size:22px;color:orange;cursor:pointer;'></i></div>
<p><?= $product->description ?></p>
</div>

<?php if(\Yii::$app->user->isGuest): ?>
<div class='text-center'>
<small class="text-danger">Чтобы оставить комментарий нужно авторизоваться</small>
</div>
<?php endif; ?>

<div class='product-comments' >
<h4>Комментарий (<?= $pages->totalCount ?>)</h4>

<?php foreach($comments as $comment): ?>

<div class="user-comment-block mt-5" data-id='<?= $comment->id ?>'>

    <div class="row">
<div class="user-comment-avatar col-2">
    <?= Html::img("@web/images/{$comment->user->avatar}",['width' => 100, 'style' => 'border-radius:20px;']) ?>

    <?php if($user_profile->checkUserStatus(Yii::$app->user->identity->last_active)): ?>
    <div><span style='color:green;padding:1px 20px;margin-right:5px;border:1px solid green;display:inline-block;'>Онлаин</span></div>
    <?php else: ?>
        <div><span style='color:black;padding:1px 20px;margin-rigth:5px;border:1px solid black;display:inline-block;'>Оффлаин</span></div>
    <?php endif; ?>
</div>


<div class="user-comment col-10">
    <div class="user-comment-info d-flex justify-content-between align-items-center">
        <a href='<?= \yii\helpers\Url::to(['user/view','id' => $comment->user->id]) ?>'><p><b><?= $comment->user->fio ?></b></p></a>
        <p><?= $model->convertTime($comment->created_at)?></p>
    </div>
    <div class="user-comment-text" style='display:flex;justify-content:space-between;border:1px solid silver;border-radius:10px;padding:20px; '>
      <p><?= $comment->text ?></p>
     <?php if($comment->created_at!=$comment->updated_at): ?> <small class='mt-5'>Last update: <?= $model->convertTime($comment->updated_at) ?></small> <?php  endif; ?>
    </div>
</div>

       </div>

       <div class="row">
          <div class="col-3"></div>
          <div class="col-5 mt-2"> <i class="bi bi-hand-thumbs-up-fill mr-1 icon_like" data-id='<?= $comment->id ?>' <?php if($comment->checkUserLike($comment->id,Yii::$app->user->identity->id)) echo "style='color:red;' data-like='yes'";  ?> ></i><span id='like_count' data-id='<?= $comment->id ?>'><?php $likes=$model->countLikeComment($comment->id,$comment->user_id); echo $likes!=0 ? $likes : ''; ?></span></div>

       <div class="col-4">
           <?php if(!Yii::$app->user->isGuest): ?>
 <div class="user-rules">
     <?php if($model->checkUserComment($comment->id,Yii::$app->user->identity->id)): ?> <a class='btn btn-link text-success edit-link' data-comment='<?= $comment->text ?>' data-id=<?= $comment->id ?>>Редактировать</a> <?php endif; ?>
      <a class='btn btn-link text-dark hide-link'  data-id=<?= $comment->id ?>>Скрыть</a>
      <a class='btn btn-link text-danger remove-link'  data-id=<?= $comment->id ?>>Удалить</a>
</div>

           <?php endif; ?>
       </div>
             </div>

    </div>

    <hr>

<?php endforeach; ?>

  </div>
</div>


<?php if(!Yii::$app->user->isGuest): ?>
  <?php if((\Yii::$app->request->get('page') && \Yii::$app->request->get('page')==$pages->pageCount)
           || ($pages->pageSize>=$pages->totalCount)): ?>
<div class="send-comment" style='display:block;'>

<?php $form=ActiveForm::begin([
    'id' => 'send-comment-form',
    'validateOnBlur' => false,
    'validateOnChange' => false
]) ?>

<?= $form->field($model,'text')->textArea(['rows' => 5])->label(false) ?>
<?= $form->field($model,'type')->hiddenInput(['value' => 'add_comment'])->label(false) ?>

<?= Html::submitButton('Оставить коментарий',['class' => 'btn btn-primary btn-comment']) ?>

<?php ActiveForm::end() ?>

</div>

<?php endif; ?>

<div class="edit-comment" style='display:none;'>

<?php $form=ActiveForm::begin([
    'id' => 'edit-comment-form',
    'validateOnBlur' => false,
    'validateOnChange' => false
]) ?>

<?= $form->field($model,'text')->textArea(['rows' => 5,'id' => 'edit-comment-text'])->label(false) ?>
<?= $form->field($model,'id')->hiddenInput(['class' => 'edit-comment-id'])->label(false) ?>
<?= $form->field($model,'type')->hiddenInput(['value' => 'edit_comment'])->label(false) ?>

<?= Html::submitButton('Редактировать коментарий',['class' => 'btn btn-primary btn-edit mr-4']) ?>
<?= Html::button('Отменить',['class' => 'btn btn-success btn-cancel']) ?>

<?php ActiveForm::end() ?>

</div>


<?php endif; ?>

<div class="col-12 mt-4">
    <?= \yii\bootstrap4\LinkPager::widget([
        'pagination' => $pages,
    ]) ?>
</div>



</div>

<?php
$js = <<<JS

if(sessionStorage.getItem('status_page')){
    window.scroll({
        top:document.getElementById('send-comment-form').offsetTop,
        behavior:'smooth'
    });
    sessionStorage.removeItem('status_page');
}


  

let btn_comment_edit=document.querySelector('.btn-edit');
let btn_comment_send=document.querySelector('.btn-comment');

let send_comment_form=$('#send-comment-form');
let edit_comment_form=$('#edit-comment-form');

function commentForm(form)
{
    var data = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: data,
        success: function(res){
            if(res.status=='ok' && res.page_status=='default') location.reload();
            if(res.status=='ok' && res.page_status=='next') nextPage();
           console.log(res);
        }
    });
    return false;
}

if(btn_comment_edit){
 btn_comment_edit.onclick=function()
  {
    edit_comment_form.on('beforeSubmit',commentForm(edit_comment_form));
  }
}

if(btn_comment_send){
btn_comment_send.onclick=function()
  {
    send_comment_form.on('beforeSubmit',commentForm(send_comment_form));
  }
}

function nextPage()
{
    let location=window.location.search;
    if(location.includes('page')){
      let page=location.substr(location.indexOf('page')+5);
      page='page='+(parseInt(page)+1);
      let new_search=location.replace(/page=\d+/,page);
      let new_location=window.location.origin+window.location.pathname+new_search;

      sessionStorage.setItem('status_page',true);
      window.location=new_location;
    }
    else{
        sessionStorage.setItem('status_page',true);
        window.location=window.location.href+'&page=2';
    }
   
}


JS;

$this->registerJs($js);

?>


<style>

.icon_like{
    cursor:pointer;
}

</style>

<script>

let send_comment_div=document.querySelector('.send-comment');
let edit_comment_div=document.querySelector('.edit-comment');

let edit_links=document.querySelectorAll('.edit-link');
let edit_textarea=document.getElementById('edit-comment-text');
let edit_comment_id=document.querySelector('.edit-comment-id');

let btn_cancel=document.querySelector('.btn-cancel');
let btn_edit=document.querySelector('.btn-edit');

let send_comment_form=document.getElementById('send-comment-form');
let edit_comment_form=document.getElementById('edit-comment-form');

let comments=document.querySelector('.product-comments');


comments.onclick=function(e){
    if(e.target.classList.contains('edit-link')){
        e.target.closest('.user-comment-block').appendChild(document.querySelector('.edit-comment'));
    }
};

let user_favorites=document.querySelector('.user-favorites');

user_favorites.onclick=function(e){
    if(e.target.tagName=='I'){
            if(e.target.classList.contains('user-favorites')){
                let type='';
                if(e.target.getAttribute('data-favorites')==1) type='remove_favorites';
                else type='add_favorites';
                $.ajax({
                    url:window.location.origin+'/product/favorites-product',
                    method:'GET',
                    data:{ product_id:e.target.getAttribute('data-id'),type:type_favorites },
                    success:function(res){ 
                        if(res){
                            if(type=='add_favorites'){
                                e.target.classList.remove('bi-shield');
                                e.target.classList.add('bi-shield-fill');
                                e.target.setAttribute('data-favorites',1);
                            }

                            if(type=='remove_favorites'){
                                e.target.classList.remove('bi-shield-fill');
                                e.target.classList.add('bi-shield');
                                e.target.setAttribute('data-favorites',0);
                            }
                        }
                     }
                });
         }
      }
}


/*
window.location=window.location.href+'#send-comment-form';
*/

/*

let cords = ['scrollX','scrollY'];
// Перед закрытием записываем в локалсторадж window.scrollX и window.scrollY как scrollX и scrollY
window.addEventListener('unload', e => cords.forEach(cord => localStorage[cord] = window[cord]));
// Прокручиваем страницу к scrollX и scrollY из localStorage (либо 0,0 если там еще ничего нет)
window.scroll(...cords.map(cord => localStorage[cord]));

*/

for(let i=0;i<edit_links.length;i++){

    edit_links[i].onclick=function()
    {
        edit_textarea.value=this.getAttribute('data-comment');
        edit_comment_id.value=this.getAttribute('data-id');
   
         if(send_comment_div) send_comment_div.style.display='none';
         if(edit_comment_div) edit_comment_div.style.display='block';
    }
}


btn_cancel.onclick=function()
{
edit_comment_form.reset();
if(send_comment_div) send_comment_div.style.display='block';
if(edit_comment_div) edit_comment_div.style.display='none';
}



let icon_likes=document.querySelectorAll('.icon_like');
let like_count=document.querySelectorAll('#like_count');

for(let i=0;i<icon_likes.length;i++){
    icon_likes[i].onclick=function()
    {
        let comment_id=this.getAttribute('data-id');
        let user_id='<?= \Yii::$app->user->identity->id ?>';
        let data={
            comment_id:comment_id,
            user_id:user_id
        };

if(!this.getAttribute('data-like')){

     data.type='add_like';

    $.ajax({
           url:'like',
           method:'GET',
           data: { info:JSON.stringify(data) },

           success:function(res) {
          if(res){
              res=JSON.parse(res);
              
              for(let j=0;j<icon_likes.length;j++){
                  if(icon_likes[j].getAttribute('data-id')==res.comment_id){
                    icon_likes[j].style.color='red';
                    icon_likes[j].setAttribute('data-like','yes');
                    if(like_count[j].innerHTML!='') like_count[j].innerHTML=parseInt(like_count[j].innerHTML)+1;
                    else like_count[j].innerHTML=1;
                  }
              }

          }

             }
        });
}

else{

    data.type='remove_like';

    $.ajax({
       url:'like',
       method:'GET',
       data: { info: JSON.stringify(data) },
       success: function(res){
        res=JSON.parse(res);
        for(let j=0;j<icon_likes.length;j++){
            if(icon_likes[j].getAttribute('data-id')==res.comment_id){
                icon_likes[j].style.color='#212529';
                if(like_count[j].innerHTML) like_count[j].innerHTML=parseInt(like_count[j].innerHTML)-1;
                if(like_count[j].innerHTML==0) like_count[j].innerHTML='';
                icon_likes[j].removeAttribute('data-like');
            }
        }
       }
    });

}

        

    }
}


</script>

