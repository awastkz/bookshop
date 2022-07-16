<?php

use yii\bootstrap4\Html;

$this->title='Профиль пользователя';

?>
<div class='row'>

    <div class="col-4 text-center" style='border:1px solid silver;'>
    <div class="user-role">Пользователь сайта <hr></div>
    <div class="user-avatar"><?= Html::img("@web/images/{$user->avatar}",['width' => '200','style' => 'border-radius:100%;']) ?> <hr></div>
    <div class="user-info"><?= $user->fio ?></div>
    </div>
     <div class="col-1"></div>
    <div class="col-7 text-center" style='border:1px solid silver;'>
    <?= $this->render('//layouts/inc/user_profile',compact('userInfo')) ?>
    </div>

</div>
<div class='mt-2 mb-5'>
  <?php if(!\Yii::$app->user->isGuest): ?>
    <?php if($user_profile->isSubscribe($user->id)): ?>
        <a href='<?= \yii\helpers\Url::to(['user/unsubscribe','id' => $user->id]) ?>' class="btn btn-sm btn-outline-danger">Отписаться</a>
        <?php else: ?>
            <a href='<?= \yii\helpers\Url::to(['user/subscribe','id' => $user->id]) ?>' class="btn btn-sm btn-outline-success">Подписаться</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
      <div><small class="text-success mb">Чтобы подписаться нужно авторизоватся</small></div><br>
    <?php endif; ?>

<div class='users_modal'>
    <button class="btn-primary btn_users_modal" data-title='Подписчики' data-type='followers' data-id='<?= $user->id ?>' data-toggle="modal" data-target="#exampleModal">Подписчики: <?= $user_profile->countFollowers($user->id) ?></button>
    <button class="btn-primary btn_users_modal" data-title='Подписки' data-type='subscriptions' data-id='<?= $user->id ?>' data-toggle="modal" data-target="#exampleModal">Подписки: <?= $user_profile->countSubscriptions($user->id) ?></button>
    <button class="btn-primary btn_users_modal" data-title='Общие знакомые' data-type='mutual_familiar' data-id='<?= $user->id ?>' data-toggle="modal" data-target="#exampleModal">Общие знакомые: <?= $user_profile->countMutualFamiliar($user->id) ?></button>

    <div class="users-modal-info">
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Выход</button>
      </div>
    </div>
  </div>
</div>
    </div>

</div>

<script>

    let users_modal=document.querySelector('.users_modal');
    let users_modal_body=users_modal.querySelector('.modal-body');
    let users_modal_title=users_modal.querySelector('.modal-title');

    users_modal.onclick=function(e)
    { 
        if(e.target.classList.contains('btn_users_modal') && e.target.tagName=='BUTTON'){

          users_modal_title.innerHTML=e.target.getAttribute('data-title');

            $.ajax({
                url:window.location.origin+'/user/show-modal-users',
                method:'GET',
                data:{ id:e.target.getAttribute('data-id'),type:e.target.getAttribute('data-type') },
                success:function(res){
                    users_modal_body.innerHTML=res;
                }
            });
        }
    }

</script>