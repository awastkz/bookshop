<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title='Оформить заказ';

?>
<div class="col-12">
<div class="row">
    <div class="col-2"></div>

    <div class="col-10">
    <div class="checkout">
<div class="checkout-cart">
     <?= $this->render('//cart/checkout-cart',compact('session','order')) ?>
</div>

<?php if(!empty($session['cart'])):  ?>
<div class="customer-info">
    <div style='text-align:center;'><h4>Покупка</h4></div>
    <?php $form=ActiveForm::begin() ?>
    <?php if(\Yii::$app->user->isGuest): ?>
    <?= $form->field($order,'name')->textInput() ?>
    <?= $form->field($order,'email')->textInput() ?>
    <?php endif; ?>
    <?= $form->field($order,'address')->textInput() ?>
    <?= $form->field($order, 'phone')->widget(\yii\widgets\MaskedInput::class, [
       'mask' => '+7 (999) 999-99-99',
       'clientOptions' => [                   
        'clearIncomplete' => true,
        'clearMaskOnLostFocus' => false,
    ]
]) ?>
    <?= $form->field($order,'note')->textarea() ?>
    <?= Html::submitButton('Оформить заказ',['class' => 'btn btn-success']) ?>
    <?php ActiveForm::end() ?>
</div>
<?php endif; ?>

</div>
    </div>
    <script>

let checkout_cart=document.querySelector('.checkout-cart');


function showCheckoutCart(res)
{
    checkout_cart.innerHTML=res;
}


checkout_cart.onclick=function(e){
 
    if(e.target.tagName='BUTTON'){

        data={
            product_id:e.target.getAttribute('data-id'),
            calc:e.target.getAttribute('data-calc'),
        };


     if(data.product_id && data.calc){
        $.ajax({
            url:'calc',
            method:'GET',
            data: { info:JSON.stringify(data) },
            success:function(res){ showCheckoutCart(res); }  
        });
       }
    }

    if(e.target.tagName=='A'){
        if(e.target.classList.contains('btn-cart-delete')){
            let id=e.target.getAttribute('data-id');
            let type='checkout';
            if(id){
                $.ajax({
                   url:'remove',
                   method:'GET',
                   data: { id:id, type:type },
                   success:function(res){ checkout_cart.innerHTML=res; }
                });
            }
        }
    }
}

</script>

    </div>
</div>


