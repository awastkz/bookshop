<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use kartik\number\NumberControl;
use app\models\ProductFilter;

?>

<div><button class="btn btn-sm btn-primary" id='filter-btn-hide-show' data-toggle="collapse" data-target="#filter-block"><?= Yii::t('navigation','Filter') ?></button>
<?php if(ProductFilter::hasFilterSession($session)): ?>
     <?= Html::beginForm(\yii\helpers\Url::to(['main/reset-filter']),'post',['style' => 'display:inline-block;']) ?>
     <?= Html::submitButton('Сбросить фильтр',['class' => 'btn btn-sm btn-danger']) ?>
     <?= Html::endForm() ?>
<?php endif; ?>
</div>


    <div class="filter-block collapse col-sm-10" id='filter-block' style='padding:20px;'>

    <?php $form=ActiveForm::begin([
        'validateOnBlur' => false,
        'validateOnChange' => false,
    ]) ?>

          <div class="form-group row col-sm-6 col-lg-4">
            <label for="filter-category" class="text-center">Категория</label>
            <?= $form->field($productFilter_model,'category_id')->dropdownList(
                            $productFilter_model->categoryItems(),
                            [
                                'prompt' => 'Все',
                                'options' => [
                                    \Yii::$app->request->get('id') ? \Yii::$app->request->get('id') : '' => ['selected' => true],
                                ],
                            ])->label(false) ?>
          </div>

          <hr>

        <div class="row">

          <div class="row col-sm-12">

              <div class="col-xl-6 col-sm-12" id='filter-1'>
                  <div class="filter-block-1 row"><span class="mb-2" style='margin-left:35%;'>Цена</span></div>

                  <div class="filter-block-2 row">
                    <div class="form-group row col-md-6">
                        <label for="filter-price-from" class='col-form-label'>От</label>
        <div class="col-sm-10"><?= $form->field($productFilter_model,'price_from')->widget(NumberControl::className(),[
   'maskedInputOptions' => [
    'rightAlign' => false,
    'digits' => 0,
    'allowMinus' => false,
   ],
])->label(false);
?></div>
                </div>
      
                        <div class="form-group row col-md-6">
                        <label for="filter-price-to" class='col-form-label'>До</label>
            <div class="col-sm-10"><?= $form->field($productFilter_model,'price_to')->widget(NumberControl::className(),[
   'maskedInputOptions' => [
    'rightAlign' => false,
    'digits' => 0,
    'allowMinus' => false,
   ],
])->label(false); ?></div>
                        </div>
                  </div>
              </div>
              
              <div class="col-xl-6 col-sm-12">
                <div class="filter-block-1 row"><span class="mb-2" style='margin-left:30%;'>Год издательство</span></div>

                <div class="filter-block-2 row">
                  <div class="form-group row col-md-6">
                       <label for="filter-year-from" class='col-form-label'>От</label>
                       <div class="col-sm-10"><?= $form->field($productFilter_model, 'year_publish_from')->dropdownList(
                            $productFilter_model->yearRange()
                            )->label(false);
                           ?></div>
                  </div>
  
                    <div class="form-group row col-md-6">
                        <label for="filter-year-to" class='col-form-label'>До</label>
                        <div class="col-sm-10"><?= $form->field($productFilter_model, 'year_publish_to')->dropdownList(
                            $productFilter_model->yearRange(),
                            [
                                'options' => [
                                    max($productFilter_model->yearRange()) => ['selected' => true],
                                ],
                            ]
                        )->label(false);
                           ?></div>
                    </div>
                    </div>
              </div>

        </div>

    </div>

    <hr>
        

    <div class="row">

        <div class="col-sm-12 col-md-6 mb-5">

        <div class="row d-flex justify-content-center"><span class="mb-2">Акция</span></div>
        <div class="row d-flex justify-content-center">
            <div class="form-check form-check-inline">
                <?= $form->field($productFilter_model, 'discount')->radioList($productFilter_model->discountItems())->label(false); ?>
            </div>
        </div>

        </div>

        <div class="col-sm-12 col-md-6">
           <div class="row d-flex justify-content-center"><span class="mb-2">В кредит</span></div>

           <div class="row d-flex justify-content-center">
            <div class="form-check form-check-inline">
            <?= $form->field($productFilter_model, 'credit')->radioList($productFilter_model->creditItems())->label(false); ?>
            </div>
  
        </div>

        </div>

    </div>

    <hr>

    <?= Html::submitButton('Фильтровать',['class' => 'btn btn-success','id' => 'filter-btn']) ?>

    <?php ActiveForm::end() ?>

    </div>

    <script>

        let price_from=document.getElementById('productfilter-price_from-disp');
        let price_to=document.getElementById('productfilter-price_to-disp');
        let filter_btn=document.getElementById('filter-btn');
        let filter_block=document.getElementById('filter-block');
        let invalid_feedback=document.querySelector('.field-productfilter-price_from .invalid-feedback');

        let price_to_value=parseInt(price_to.value.split(',').join(''));
        let price_from_value=parseInt(price_from.value.split(',').join(''));

        priceFromValidate();

        filter_btn.onclick=function()
        {
            priceFromValidate();
        }

        function priceFromValidate()
        {
            if(price_from_value!='' && price_to_value!='' && price_from_value>price_to_value){
                
                filter_btn.setAttribute('aria-expanded',true);
                filter_block.classList.add('show');

                price_from.setAttribute('aria-invalid',true);
                price_from.classList.remove('is-valid');
                price_from.classList.add('is-invalid');
            }
            else{
                invalid_feedback.innerHTML='';
                price_from.setAttribute('aria-invalid',false);
                price_from.classList.remove('is-invalid');
            }
        }

    </script>