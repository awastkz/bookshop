<?php

use kartik\date\DatePicker;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title='Мои покупки';

?>
<div class="text-center"><h2>Мои покупки</h2></div>

<?= app\widgets\Alert::widget() ?>

<?php $form=ActiveForm::begin([
    'validateOnBlur' => false,
    'validateOnChange' => false,
]) ?>

<div class="filter-block col-sm-6 mb-5">

<div class="row filter-input">
    <div class="form-group col-sm-5">
        <?= $form->field($model,'date_from')->widget(DatePicker::class,[
	'options' => ['placeholder' => 'Выбрать дату','style' => 'cursor:pointer;'],
    'language' => 'ru',
    'readonly' => true,
    'type' => DatePicker::TYPE_INPUT,
    'convertFormat' => true,
	'pluginOptions' => [
		'format' => 'yyyy-M-d',
		'todayHighlight' => true,
        'todayBtn' => true,
        'autoclose' => true,
    ],
]); ?>
    </div>

    <div class="form-group col-sm-5">
        <?= $form->field($model,'date_to')->widget(DatePicker::class,[
	'options' => ['placeholder' => 'Выбрать дату','style' => 'cursor:pointer;'],
    'language' => 'ru',
    'readonly' => true,
    'convertFormat' => true,
    'type' => DatePicker::TYPE_INPUT,
	'pluginOptions' => [
		'format' => 'yyyy-M-d',
		'todayHighlight' => true,
        'todayBtn' => true,
        'autoclose' => true,
    ],
]); ?>
    </div>
</div>

<div class="row">

<div class="filter-btn col-sm-5">
<?= Html::submitButton('Фильтр',['class' => 'btn btn-sm btn-success col-sm-6','style' => 'margin-left:40%;']) ?>
</div>

<?php ActiveForm::end() ?>

<div class="reset-btn col-sm-7">
<?= Html::beginForm(\yii\helpers\Url::to(['user/reset-filter']),'post') ?>
<?= Html::submitButton('Сбросить фильтр',['class' => 'btn btn-sm btn-danger col-sm-6']) ?>
<?= Html::endForm() ?>
</div>

</div>

</div>

<hr>


<?php if(count($purchase)>0): ?>
<div class="user-purchase">
    <table class="table table-bordered table-hover">
        <thead>
            <th>#</th>
            <th>Номер покупки</th>
            <th>Товары</th>
            <th>Сумма</th>
            <th>Подробно о покупке</th>
            <th>Дата</th>
        </thead>
        <tbody>
            <?php foreach($purchase as $item): ?>
              <tr>
                <td><?= $item_counter ?></td>
                <td>#<?= $item->order_number ?></td>
                <td>
                    <?php foreach($item->orderProduct as $product): ?>
                        <?= $product->name.' '.$product->qty ?>шт<br>
                    <?php endforeach; ?>
                </td>
                <td><?= $item->sum ?></td>
                <td><button class="btn btn-sm btn-primary btn_purchase_detail" data-toggle='modal' data-target='#purchase-modal' data-number='<?= $item->order_number ?>' data-id='<?= $item->id ?>'>Подробнее</button></td>
                <td><?= $item->created_at ?></td>
              </tr>
              <?php $item_counter++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="col-12 mt-4">
    <?= \yii\bootstrap4\LinkPager::widget([
        'pagination' => $pages,
    ]) ?>
</div>

<?php else: ?>
    <div class="text-center"><h2>У вас нет покупок</h2></div>
<?php endif; ?>

<div class="modal fade" id="purchase-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel">Покупка</h4>
                          <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      </div>
                      <div class="modal-body" style='text-align:center;'>
                    </div>
                      <div class="modal-footer">
                      <button class="btn btn-dark" data-dismiss='modal'>Выход</button>
                      </div>
                 </div>
            </div>
</div>


<script>
    
    let user_purchase_block=document.querySelector('.user-purchase');
    let purchase_modal_body=document.querySelector('#purchase-modal .modal-body');
    let purchase_modal_title=document.querySelector('#purchase-modal .modal-title');

    user_purchase_block.onclick=function(e)
    {
        if(e.target.classList.contains('btn_purchase_detail')){
            purchase_modal_title.innerHTML='Покупка #'+e.target.getAttribute('data-number');

            $.ajax({
                url:window.location.origin+'/user/show',
                method:'GET',
                data:{ order_id:e.target.getAttribute('data-id') },
                success:function(res){
                    purchase_modal_body.innerHTML=res;
                }
            });
        }
    }
</script>


