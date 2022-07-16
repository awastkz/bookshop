<?php

use yii\helpers\Html;

?>

<div class="row card-products">

<?php foreach($products as $product): ?>

    <div class="col-md-6">
<div class="card text-center">
  <div class="card-img" style='position:relative;'>
  <?= Html::img("@web/images/{$product->picture}",['style' => 'max-width:100%;']) ?>

  <?php if($product_model->isProductCredit($product->credit_id)): ?>
  <div class="card-credit">Рассрочка<br><?= $product_model->creditText($credit_items,$product->credit_id) ?></div>
  <?php endif; ?>

  <?php if($product_model->isProductDiscount($product->is_discount)): ?>
  <div class="card-discount">Скидка <?= $product_model->discountText($product->old_price,$product->price) ?>%</div>
  <?php endif; ?>

</div>
  <div class="card-body">
    <a href='<?= \yii\helpers\Url::to(['product/view', 'id' => $product->id]) ?>'><h5 class="card-title"><?= $product->name ?></h5></a>
    <p class="card-text"><?= $product->description ?></p>
    <p><span class='text-dark'><s><?= $product->old_price ?></s>tg</span> <span class='text-success'><?= $product->price ?>tg</span></p>
    <a data-id=<?= $product->id ?> class="btn btn-primary card-btn">В корзину</a>
    <p class='mt-3'><i <?= $favorites_model->checkFavorites($product->id,$favorites_items) ? 'class="favorites-icon bi bi-shield-fill" data-favorites="1" data-toggle="tooltip" data-placement="bottom" title="Удалить из избранных" ' : '' ?> class="favorites-icon bi bi-shield" data-toggle="tooltip" data-placement="bottom" title="Добавить в избранное" data-id='<?= $product->id ?>' style='font-size:22px;color:orange;cursor:pointer;'></i></p>
    <div><span style='margin-left:60%;'>просмотров: <b><?= $product->views ?></b></span></div>
  </div>
</div>
</div>
    <?php endforeach; ?>
    
    </div>