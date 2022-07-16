<?php

use yii\bootstrap4\Html;

?>
<?php if(!empty($session['cart'])): $count=1;  ?>

<table class="table table-bordered checkout-cart-table">
    <thead>
        <td>№</td>
        <th>Рисунок</th>
        <th>Название</th>
        <th>Количество</th>
        <th>Цена за шт</th>
        <th>Сумма товара</th>
        <th>Удалить</th>
    </thead>

    <tbody>

            <?php foreach($session['cart'] as $cart => $product): ?>

               <tr>
                   <td><?= $count++; ?></td>
                   <td><?= Html::img("@web/images/{$product['picture']}",['width' => 200]) ?></td>
                   <td><?= $product['name'] ?></td>
                   <td><button class="btn btn-danger" data-id='<?= $product['id'] ?>' data-calc='minus'>-</button><input type="text" class='m-2' style='width:50px;' id="product_qty" readonly value='<?= $product['qty'] ?>'><button class="btn btn-success" data-id='<?= $product['id'] ?>' data-calc='plus'>+</button></td>
                   <td><?= $product['price'] ?></td>
                   <td><?= $product['price']*$product['qty'] ?></td>
                   <td><a class='btn btn-link btn-cart-delete' data-id='<?= $product['id'] ?>' style='cursor:pointer;color:red;text-decoration:none;font-weight:bold;font-size:18px;'>X</a></td>
               </tr>

                <?php endforeach; ?>
           
                <?php else: ?>
                   <div class='text-center'><h3>Корзина пуста</h3></div>
          <?php endif; ?>
    </tbody>
</table>

<div class="checkout-result" style='font-weight:bold;'>
  <?php if(!empty($session['cart'])): $count=1; ?>
    <?php foreach($session['cart'] as $cart => $product): ?>
        <p><?= $count++.')' ?><?= $product['name'] ?> <?= $product['qty'] ?>шт <span><?= $product['qty']*$product['price'] ?>тг</span></p>
        <?php endforeach; ?>
        <p>Общая сумма: <?= $session['cart.sum'] ?>тг</p>
    <?php endif; ?>
</div>
