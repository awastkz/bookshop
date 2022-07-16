<?php

use yii\bootstrap4\Html;
use app\models\User;
use Faker\Factory;

$this->title=Yii::$app->name;

?>

<div class="row">
<?= $this->render('//layouts/inc/product_filter',compact('productFilter_model','session')) ?>
</div>

<div class="row">
    <div class="col-md-4">
<?= $this->render('//layouts/inc/sidebar') ?>
    </div>

    
<div class="col-md-8">
<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
  <li class="nav-item col-sm-4" role="presentation">
    <a class="btn btn-outline-success active col-sm-12 mb-2" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true"><?= Yii::t('tabulation','All products') ?></a>
  </li>
  <li class="nav-item col-sm-4" role="presentation">
    <a class="btn btn-outline-success col-sm-12 mb-2" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false"><?= Yii::t('tabulation','Discount') ?></a>
  </li>
  <li class="nav-item col-sm-4" role="presentation">
    <a class="btn btn-outline-success col-sm-12 mb-2" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false"><?= Yii::t('tabulation','Sale') ?></a>
  </li>
</ul>

<div class="tab-content" id="pills-tabContent">
  <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
    <div class='text-center mb-4'><h5><?= Yii::t('tabulation','All products') ?></h5></div>

    <?= $this->render('//main/product-card',[
        'products' => $products,
        'pages' => $pages,
        'favorites_items' => $favorites_items,
        'favorites_model' => $favorites_model,
        'productFilter_model' => $productFilter_model,
        'session' => $session,
        'product_model' => $product_model,
        'credit_items' => $credit_items,
      ]) ?>

      <div class="col-12 mt-4">
    <?= \yii\bootstrap4\LinkPager::widget([
        'pagination' => $pages,
    ]) ?>
       </div>
       
  </div>

  <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
  <div class='text-center mb-4'><h5><?= Yii::t('tabulation','Discount') ?></h5></div>

  <?= $this->render('//main/product-card',[
        'products' => (object)$discountProducts,
        'pages' => $pages,
        'favorites_items' => $favorites_items,
        'favorites_model' => $favorites_model,
        'productFilter_model' => $productFilter_model,
        'session' => $session,
        'product_model' => $product_model,
        'credit_items' => $credit_items,
      ]) ?>

  </div>
  <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
  <div class='text-center mb-4'><h5><?= Yii::t('tabulation','Sale') ?></h5></div>

  <?= $this->render('//main/product-card',[
        'products' => $saleProducts,
        'pages' => $pages,
        'favorites_items' => $favorites_items,
        'favorites_model' => $favorites_model,
        'productFilter_model' => $productFilter_model,
        'session' => $session,
        'product_model' => $product_model,
        'credit_items' => $credit_items,
      ]) ?>
  </div>
</div>


 </div>


</div>



