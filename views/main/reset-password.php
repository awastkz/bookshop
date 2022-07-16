<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>
<?php if(!Yii::$app->session->hasFlash('error')): 
         if(!Yii::$app->session->hasFlash('success')):  ?>
<?php $form=ActiveForm::begin() ?>

<?= $form->field($model,'password')->passwordInput() ?>
<?= $form->field($model,'repeat_password')->passwordInput() ?>

<?= Html::submitButton('Изменить пароль',['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>

<?php endif; endif; ?>


<?php if(Yii::$app->session->hasFlash('success') || Yii::$app->session->hasFlash('error')): ?>
    <div class='text-center'><a href="<?= \yii\helpers\Url::to(Yii::$app->homeUrl) ?>" class="btn btn-primary">На главную</a></div>
<?php endif; ?>