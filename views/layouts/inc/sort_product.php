<?php

use app\models\ProductSort;
use yii\helpers\Url;

?>
<ul class="navbar-nav">
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= Yii::t('navigation','Sort') ?>
                  </a>
                  
                  <div class="dropdown-menu" aria-labelledby="navbarDropdown2">

                  <?php foreach(ProductSort::productSortList() as $item): ?>
                  <a href="<?= \yii\helpers\Url::current(['sort' => $item['sort']]) ?>"
                  class="dropdown-item <?= ProductSort::isActiveSort($item['sort'],$sort) ?>">
                  <?= Yii::t('navigation',$item['title']) ?></a>
                  <?php endforeach; ?>
                  
                  </div>
                </li>
              </ul>