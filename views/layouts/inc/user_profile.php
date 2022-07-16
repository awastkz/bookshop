<div class="user-header"><b>Профиль</b><hr></div>
      <table class="table table-bordered">
          <tr><td>Регистрация: <?= $userInfo['register_date'] ?></td></tr>
          <tr><td>Статус: <?= $userInfo['status'] ? '<span style="color:green;">Online</span>' : '<span style="color:black;">Offline</span>' ?></td></tr>
          <?php if($userInfo['last_active']): ?><tr><td>Последняя активность: <?= $userInfo['last_active'] ?></td></tr> <?php endif; ?>
          <tr><td>E-mail: <?= $userInfo['email'] ?></td></tr>
          <tr><td>Заказы: <?= $userInfo['orders'] ?></td></tr>
          <tr><td>Кол-во товаров: <?= $userInfo['products_count'] ?></td></tr>
          <tr><td>Сумма покупок: <?= $userInfo['total_sum'] ?></td></tr>
      </table>