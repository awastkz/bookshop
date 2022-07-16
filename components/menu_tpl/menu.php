<li class="nav-item nav-item-sidebar" <?= $category['children'] ? 'id="parent"' : '' ?> data-id='<?= $category['id'] ?>'>

		<a class="nav-link nav-link-sidebar" href="<?= \yii\helpers\Url::to(['category/view','id' => $category['id']]) ?>" 
        <?= $category['children'] ? 'data-bs-toggle="collapse"  data-bs-target="#menu_item'.$category['id'].'"' : '' ?>>
        <?= $category['name'] ?> <?= $category['children'] ? '<i class="bi small bi-caret-down-fill"></i>' : '('.$this->countCategoryProducts($category['id']).')' ?> </a>
        
        <?php if($category['children']): ?>

         <ul id="menu_item<?= $category['id'] ?>" class="submenu collapse" data-bs-parent="#nav_accordion">
    
            <?php if(count($category['children'])>1): ?>
            <li class="nav-item nav-item-sidebar" data-id='<?= $category['id'] ?>'><a href="<?= \yii\helpers\Url::to(['category/view','id' => $category['id']]) ?>" class="nav-link nav-link-sidebar"><?= $category['name'] ?></a></li>
            <?php endif;?>

			<?= $this->getMenuHtml($category['children']) ?>
		</ul>
            <?php endif; ?>
</li>

