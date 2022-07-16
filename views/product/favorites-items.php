<?php

use yii\bootstrap4\Html;

$this->title='Избранное';

?>
<?php if(!empty($products)): ?>
<div class='text-center'><h2>Избранное</h2></div>
<?php foreach($products as $product): ?>
<div class="favorites-item mt-4 col-10 table-bordered">
  <div class="row">

    <div class="favorites-img col-3">
    <?= Html::img("@web/images/{$product->picture}",['width' => 200]) ?>
    </div>

    <div class="favorites-description col-9">
      <div class="row">
        <div class="favorites-description-left mt-2 col-6">
            <a href="<?= \yii\helpers\Url::to(['product/view','id' => $product->id])?>"><?= $product->name ?></a>
            <p><?= $model->getCategoriesItems($product->category_id,$model->getCategoriesList()) ?>
               <?= $model->clearCategoriesItems() ?></p>
        </div>
        <div class="favorites-description-right mt-2 col-6 text-right">
            <p><?= $product->price ?>р</p>
            <i class="product-favorites bi bi-shield-fill" data-id='<?= $product->id ?>' style='font-size:22px;cursor:pointer;display:inline-block;margin-top:60px;'></i>
        </div>
      </div>
    </div>

  </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php if($pages->totalCount==0): ?>
  <div class='text-center'><h2>У вас нет избранных товаров</h2></div>
<?php endif; ?>

<div class="col-12 mt-4">
    <?= \yii\bootstrap4\LinkPager::widget([
        'pagination' => $pages,
    ]) ?>
</div>

<div class="page-favorites" style='display:none;'><?= $page_status ?></div>