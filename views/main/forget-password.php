<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

?>

<?php $form=ActiveForm::begin() ?>

<?= $form->field($model,'email')->textInput() ?>

<?= Html::submitButton('Восстановить пароль',['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>