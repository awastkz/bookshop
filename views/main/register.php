<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\captcha\Captcha;

?>
<div class="register-block col-sm-6 m-auto">

<div style='text-align:center;'><h2>Регистрация</h2></div>
<?php $form=ActiveForm::begin([
]) ?>

<?= $form->field($model,'fio')->textInput() ?>
<?= $form->field($model,'email')->textInput() ?>
<?= $form->field($model,'password')->passwordInput() ?>
<?= $form->field($model,'password_repeat')->passwordInput() ?>
<?= $form->field($model, 'captcha')->widget(
        yii\captcha\Captcha::className(),
        [
            'captchaAction' => 'main/captcha',
            'template' => '<div class="row"><div class="col-xs-3">{image}</div><div class="col-xs-4">{input}</div></div>'
        ]
    )->hint('Нажмите картинку чтобы обновить'); ?>

<?= Html::submitButton('Регистрация',['class' => 'btn btn-primary'])?>

<?php ActiveForm::end() ?>

</div>
