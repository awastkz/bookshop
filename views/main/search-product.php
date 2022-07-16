

<div class="row">

    <div class="col-md-4">
        <?= $this->render('//layouts/inc/sidebar') ?>
    </div>

    <div class="col-md-8">
    <?php if(count($products)>0): ?>
<div class="text-center"><h1>Поиск </h1><h4>(Найдено <?= count($products) ?> товара)</h4></div><hr>
<div class="text-center"></div><br>


<?= $this->render('//main/product-card',[
    'products' => $products,
    'favorites_items' => $favorites_model->getFavoritesData(),
        'favorites_model' => $favorites_model,
        'productFilter_model' => $productFilter_model,
        'session' => $session,
        'product_model' => $product_model,
        'credit_items' => $product_model->creditItems(),
]) ?>

<?php else: ?>

    <div class="text-center"><h2>По поиску ничего не найдено</h2></div>


<?php endif; ?>
    </div>


</div>


