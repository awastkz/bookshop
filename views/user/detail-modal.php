<?php

use yii\helpers\Html;
$count=0;

?>
<table class="table table-bordered">

     <thead>
        <th>#</th>
        <th>Название товара</th>
        <th>Рисунок</th>
        <th>Автор</th>
        <th>Цена</th>
        <th>Количество</th>
        <th>Сумма</th>
    </thead>

    <tbody>
        <?php foreach($detail_items as $item): ?>
            <tr>
                <td><?= ++$count ?></td>
                <td><a href='<?= \yii\helpers\Url::to(['product/view','id' => $item->product_id]) ?>'><?= $item->name ?></a></td>
                <td><?= Html::img("@web/images/{$item->product->picture}",['width' => 200]) ?></td>
                <td><?= $item->author ?></td>
                <td><?= $item->price ?></td>
                <td><?= $item->qty ?></td>
                <td><?= $item->price*$item->qty ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    
    <tfoot>
    <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Общее количество</th>
            <th>Общая сумма</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th><?= $user_purchase->totalQuantity($order_id) ?></th>
            <th><?= $user_purchase->totalSum($order_id) ?></th>
        </tr>
    </tfoot>
</table>