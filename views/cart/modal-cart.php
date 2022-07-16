<?php

use yii\bootstrap4\Html;

?>

<div class="modal-cart-block">

<?php if(!empty($session['cart'])): ?>

<table class='table'>
    <thead>
        <th>Рисунок</th>
        <th>Название</th>
        <th>Кол-во</th>
        <th>Цена за шт</th>
        <th>Сумма товара</th>
        <th>Удалить</th>
    </thead>
<tbody>
<?php foreach($session['cart'] as $cart => $product ): ?>
<tr>
    <td><?= Html::img("@web/images/{$product['picture']}",['width' => 100]) ?></td>
    <td><?= $product['name'] ?></td>
    <td><?= $product['qty'] ?></td>
    <td><?= $product['price'] ?></td>
    <td><?= $product['price']*$product['qty'] ?></td>
    <td><a class='btn btn-link btn-modal-delete' data-id='<?= $product['id'] ?>' style='cursor:pointer;color:red;text-decoration:none;font-weight:bold;font-size:18px;'>X</a></td>
</tr>

<?php endforeach; ?>
</tbody>
        
</table>

<p>Количество: <?= $session['cart.qty'] ?> шт</p>
<p>Cумма: <?= $session['cart.sum'] ?> тг</p>

<?php else: ?>
<h4>Корзина пуста</h4>

<?php endif; ?>
</div>


<div class="checkout-cart-block">
    <?= $this->render('//cart/checkout-cart',compact('session','order')) ?>
</div>