<?php

use yii\helpers\Html;

?>
<?php if(count($modal_users)>0): ?>
    <?php foreach($modal_users as $item): ?>
        <div class="row user-info">
        <?= Html::img("@web/images/{$item['avatar']}",['width' => '60','class' => 'col-sm-3','style' => 'border-radius:40px;']) ?>
        <a href="<?= \yii\helpers\Url::to(['user/view','id' => $item['id']]) ?>" class='col-sm-2 mt-4'><p><?= $item['fio'] ?></p></a>
        </div>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>