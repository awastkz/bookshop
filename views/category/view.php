<?php

use yii\bootstrap4\Html;

$this->title='Категория: '.$category->name;
$this->params['breadcrumbs'][]=['label' => $category->name];

?>
<div class='text-center'><h4>Категория: <?= $category->name ?></h4></div>

<div class="row">
<?= $this->render('//layouts/inc/product_filter',compact('productFilter_model','session')) ?>
</div>

<div class="row">
    <div class="col-md-4">
<?= $this->render('//layouts/inc/sidebar') ?>
    </div>

<div class="col-md-8">
    <div class="row card-products">

<?php foreach($products as $product): ?>

    <div class="col-md-6">
<div class="card text-center">
  <?= Html::img("@web/images/{$product->picture}") ?>
  <div class="card-body">
    <a href='<?= \yii\helpers\Url::to(['product/view', 'id' => $product->id]) ?>'><h5 class="card-title"><?= $product->name ?></h5></a>
    <p class="card-text"><?= $product->description ?></p>
    <p><span class='text-dark'><s><?= $product->old_price ?></s>tg</span> <span class='text-success'><?= $product->price ?>tg</span></p>
    <a data-id=<?= $product->id ?> class="btn btn-primary card-btn">В корзину</a>
    <p class='mt-3'><i <?= $favorites_model->checkFavorites($product->id,$favorites_items) ? 'class="favorites-icon bi bi-shield-fill" data-favorites="1"' : '' ?> class="favorites-icon bi bi-shield" data-id='<?= $product->id ?>' style='font-size:22px;color:orange;cursor:pointer;'></i></p>
    <div><span style='margin-left:60%;'>просмотров: <b><?= $product->views ?></b></span></div>
    <div><span><?= $product->created_at ?></span></div>
  </div>
</div>
</div>
    <?php endforeach; ?>

    <div class="col-12 mt-4">
    <?= \yii\bootstrap4\LinkPager::widget([
        'pagination' => $pages,
    ]) ?>
       </div>

       
    </div>
 </div>


</div>

<script>

function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

let category_id=getParameterByName('id');

let nav_items=document.querySelectorAll('.nav-item-sidebar');
let nav_links=document.querySelectorAll('.nav-link-sidebar');

for(let i=0;i<nav_items.length;i++){
    if(nav_items[i].getAttribute('data-id')==category_id){
        if(nav_items[i].id!='parent') nav_items[i].style.borderLeft='2px solid orange';
        let parent_link=nav_items[i].closest('#parent');
        if(parent_link!==null){
            let parent_link_a=parent_link.querySelector('a');
            let parent_link_ul=parent_link.querySelector('ul');
            parent_link_a.setAttribute('aria-expanded',true);
            parent_link_ul.classList.add('show');
        }
    }
}






</script>