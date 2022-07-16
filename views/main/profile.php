<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title='Профиль пользователя';

?>
<div class='text-center mb-4'><h2>Профиль пользователя</h2></div>
<div class="row">

<div class="user-edit-profile col-sm-5" style='border:1px solid silver;border-radius:10px;padding:15px;'>

<div class="user-img-block" style='text-align:center;margin-bottom:50px;'>
<div class="user-img" style='width:250px;border:1px solid black;border-radius:20px;'>
<?= Html::img("@web/images/{$model->avatar}",['class' => 'img-responsive','width' => '100%', 'style' => 'border-radius:20px;']) ?>
</div>

<?php $form=ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data',
    ]
]) ?>
<?= $form->field($avatarModel,'avatar')->fileInput()->label(false) ?>

<div class="row">
<div class="col-sm-4">
<?= Html::submitButton('Сохранить фото',['class' => 'btn btn-sm btn-success']) ?>
<?php ActiveForm::end() ?>
</div>

<div class="col-sm-4">
<?= Html::beginForm(['main/remove-avatar','id' => \Yii::$app->user->id],'post') ?>
<?= Html::submitButton('Удалить фото',['class' => 'btn btn-sm btn-danger mr-5']) ?>
<?= Html::endForm() ?>
</div>

</div>

</div>



<div class="user-profile">
    <?php $form2=ActiveForm::begin([
        'validateOnBlur' => false,
        'validateOnChange' => false,
    ]) ?>
    <?= $form2->field($userProfile,'fio')->textInput(['value' => $userProfile->getFio()]) ?>
    <?= $form2->field($userProfile,'password')->textInput() ?>
    <?= $form2->field($userProfile,'password_repeat')->textInput() ?>
    <?= Html::submitButton('Сохранить',['class' => 'btn btn-success']) ?>
    <?php ActiveForm::end() ?>
</div>

</div>

<div class="col-sm-1"></div>

<div class="user-info-profile text-center col-sm-6" style='border:1px solid silver;border-radius:10px;padding:15px;'>
<?= $this->render('//layouts/inc/user_profile',compact('userInfo')) ?>
</div>

</div>

