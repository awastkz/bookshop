<?php

use app\assets\AppAsset;
use yii\bootstrap4\Nav;
use yii\bootstrap4\Navbar;
use yii\bootstrap4\Html;
use yii\widgets\Breadcrumbs;
use app\widgets\Alert;
use app\models\Login;
use app\components\LanguageSelector;
use app\models\Search;
use app\models\ProductSort;
use yii\bootstrap4\ActiveForm;


AppAsset::register($this);

$model=new Login();
$search_model=new Search();

?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= $this->title ?></title>
    
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?> 

<header>


 <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
        <div class="container-fluid">
        <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">BookShop</a>
        <a href="<?= \yii\helpers\Url::to(['base/portfolio']) ?>" class='btn btn-primary'>Описание портфолио</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id='navbarSupportedContent'>
            <div class="col-lg-1"></div>
              <div class="nav-cart col-lg-2 col-4">
                  <a class="btn btn-success w-100 mt-1 btn-cart-modal" data-toggle='modal' data-target='#cart-modal'><?= Yii::t('navigation','Cart') ?></a>
                </div>
              
                <div class="nav-search col-lg-4">
                 <?php $form=ActiveForm::begin([
                  'validateOnBlur' => false,
                  'validateOnChange' => false,
                  'action' => ['main/search-product'],
                'fieldConfig' => [
                  'options' => [
                    'class' => 'form-inline my-2 my-lg-0',
                  ],
                  'template' => "{input}<button class='btn btn-sm btn-outline-info my-2 my-sm-0'
                  id='search_btn' type='submit'>".Yii::t('navigation','Search')."</button>{error}",
                ],
                
              ]) ?>
                <?= $form->field($search_model,'keyword')->textInput(['class' => 'form-control w-75 mr-sm-2','autocomplete' => 'off'])->label(false) ?>
                
              <?php ActiveForm::end() ?>

              <div class="autocomplete-search"><ul></ul></div>
            </div>

         <?php if(\Yii::$app->user->isGuest): ?>
            <div class="nav-favorites">
              <div class="navbar-nav">
                <div class="nav-item">
                  <a class='nav-link' href='<?= \yii\helpers\Url::to(['product/favorites']) ?>'><?= Yii::t('navigation','Favorites') ?></a>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <div class="nav-sort">
              <?php if(ProductSort::allowSortUrl(Yii::$app->controller->id,Yii::$app->controller->action->id)): ?>
                 <?= $this->render('//layouts/inc/sort_product',['sort' => Yii::$app->request->get('sort')]) ?>
              <?php endif; ?>

            </div>

            <div class="nav-lang col-lg-1">
              <ul class="navbar-nav">
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href='#' id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= LanguageSelector::getLangTitle(Yii::$app->session->get('lang')) ?>
                  </a>
                  <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a href="<?= \yii\helpers\Url::to(['base/change-lang','lang' => 'ru-RU']) ?>" class="dropdown-item <?= Yii::$app->session->get('lang')=='ru-RU' ? 'active' : '' ?>"><span class='mr-2'>RU</span><span class="flag-icon flag-icon-ru"></span></a>
                  <a href="<?= \yii\helpers\Url::to(['base/change-lang','lang' => 'en-US']) ?>" class="dropdown-item <?= Yii::$app->session->get('lang')=='en-US' ? 'active' : '' ?>"><span class='mr-2'>EN</span><span class="flag-icon flag-icon-gb"></span></a>
                  </div>
                </li>
              </ul>
            </div>

            <?php if(!\Yii::$app->user->isGuest): ?>

              <div class="nav-avatar">
                <?= Html::img("@web/images/".Yii::$app->user->identity->avatar,['width' => 50,'style' => 'border-radius:10px;']) ?>
              </div>

            <div class="nav-account">
              <ul class="navbar-nav">
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href='#' id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <?= Yii::t('navigation','My account') ?>
                  </a>
                  <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a href="<?= \yii\helpers\Url::to(['main/profile']) ?>" class="dropdown-item" selected><?= Yii::t('navigation','Profile') ?></a>
                  <a href="<?= \yii\helpers\Url::to(['user/purchase']) ?>" class="dropdown-item"><?= Yii::t('navigation','Purchase') ?></a>
                  <a href="<?= \yii\helpers\Url::to(['product/favorites']) ?>" class="dropdown-item"><?= Yii::t('navigation','Favorites') ?></a>
                  <a href="<?= \yii\helpers\Url::to(['main/logout']) ?>" class="dropdown-item"><?= Yii::t('navigation','Logout') ?></a>
                  </div>
                </li>
              </ul>
            </div>
            <?php endif; ?>


         <?php if(\Yii::$app->user->isGuest): ?>
            <div class="nav-login-reg col-lg-2">
              <button class="btn btn-sm btn-outline-success" data-toggle='modal' data-target='#login-modal'><?= Yii::t('navigation','Login') ?></button>
              <a class="btn btn-sm btn-outline-primary" href='<?= \yii\helpers\Url::to(['main/register']) ?>'><?= Yii::t('navigation','Register') ?></a>
            </div>
            <?php endif; ?>


          </div>

        </div>
        </nav>

</header>

<main>

<?php if(ProductSort::isExistSort(Yii::$app->request->get('sort'))): ?>
  <div class='text-center mt-2'><h4><?= Yii::t('navigation','Sort') ?>: <?= Yii::t('navigation',ProductSort::getSortText(Yii::$app->request->get('sort'))) ?></h4></div>
  <?php endif; ?>

<!-- Login modal -->
<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel">Логин</h4>
                          <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      </div>
                      <div class="modal-body" style='text-align:center;'> 
                      <?php $form=ActiveForm::begin([
                          'id' => 'login_form',
                          'action' => ['main/index'],
                          'method' => 'POST',
                          'validateOnBlur' => false,
                          'validateOnChange' => false,
                      ]) ?>
                      
                          <?= $form->field($model,'login_email',[
                            'template' => '<div class="form-group">
                            {label}
                            <div class="input-group">
                                  <div class="input-group-prepend">
                                      <span class="input-group-text">
                                          <span class="fa fa-user"></span>
                                      </span>                    
                                  </div>
                                  {input}{error}
                              </div>
                          </div>'
                          ])->textInput(['placeholder' => 'E-mail']) ?>

                          <?= $form->field($model,'login_password',[
                            'enableAjaxValidation' => true,
                            'template' => '<div class="form-group">
                            {label}
                            <div class="input-group">
                                  <div class="input-group-prepend">
                                      <span class="input-group-text">
                                          <span class="fa fa-lock"></span>
                                      </span>                    
                                  </div>
                                  {input}{error}
                              </div>                    
                          </div>'
                            ])->passwordInput(['placeholder' => 'Пароль']) ?>
                          <?= $form->field($model,'rememberMe')->checkbox(['value' => '1'])->label('Запомнить') ?>
                          <?= Html::SubmitButton('Вход',['class' => 'btn btn-primary btn_submit']) ?>
                          <?php ActiveForm::end() ?>
                           <br>
                        <a href="<?= \yii\helpers\Url::to(['main/forget-password'])?>" class="btn btn-link">Забыли пароль?</a>

                      </div>
                      <div class="modal-footer">
                      <?= yii\authclient\widgets\AuthChoice::widget([
                       'baseAuthUrl' => ['main/auth'],
                       'popupMode' => false,
                       ]) ?>
                         <a href="<?= yii\helpers\Url::to(['main/register'])?>" class="btn btn-link">Регистрация</a>
                         <button class="btn btn-dark" data-dismiss='modal'>Выход</button>
                      </div>
                  </div>
              </div>
          </div>

          <style>
              .nav-search{
                position:relative;
              }

              .autocomplete-search{
                position:absolute;
                top:45px;
                left:2%;
                width:72%;
                height:auto;
                background-color:white;
                z-index:10;
                border:1px solid silver;
                
                transition:0.3s;
                display:none;
              }

              .autocomplete-search ul{
                margin:0;
                padding:0;
              }

              .autocomplete-search li{
                list-style:none;
                padding:8px;
                border-bottom:1px solid silver;
                cursor:pointer;
              }
              .autocomplete-search li:hover{
                background-color:#C1CDD6;
                color:black;
              }

              .breadcrumb{
                padding:15px;
              }
            </style>


          <!-- Cart Modal -->
<div class="modal fade" id="cart-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel">Корзина</h4>
                          <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      </div>
                      <div class="modal-body" style='text-align:center;'></div>
                      <div class="modal-footer">
                      <button class="btn btn-warning" data-dismiss='modal'>Продолжить покупки</button>
                      <a href="<?= yii\helpers\Url::to(['cart/checkout'])?>" class="btn btn-success">Оформить заказ</a>
                      <a  class="btn btn-danger btn-clear-cart">Очистить корзину</a>
                      </div>
                      </div>
                  </div>
              </div>

              
<div class="container">
<?= Breadcrumbs::widget([
                'homeLink' => ['label' => 'Главная', 'url' => '/'],
                'itemTemplate' => "<li>{link}/</li>",
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
    <?= Alert::widget() ?>
    <?= $content ?>
</div>

<?php

$login=<<<JS
$('#login_form').on('beforeSubmit', function(){
       var data = $(this).serialize();
        $.ajax({
            url: window.location.origin+'/main/index',
            type: 'POST',
            data: data,
            success: function(res){
                console.log(res);
            },
            error: function(){
                alert('Error!');
            }
        });
        return false;
    });
JS;


?>

<script>

let search_autocomplete_block=document.querySelector('.autocomplete-search');
let search_input=document.getElementById('search-keyword');
let search_autocomplete_ul=document.querySelector('.autocomplete-search ul');
let search_autocomplete_btn=document.getElementById('search_btn');


search_input.addEventListener('keyup',function(){
  $.ajax({
    url:window.location.origin+'/main/search',
    method:'GET',
    data:{ text:this.value },
    success:function(res){
      if(res.data.length>0){
        search_autocomplete_ul.innerHTML='';
        search_autocomplete_block.style.display='block';
        for(let i=0;i<res.data.length;i++){
          let li=document.createElement('li');
          li.innerHTML=res.data[i];
          search_autocomplete_ul.appendChild(li);
        }

        search_autocomplete_ul.onclick=function(e){
          if(e.target.tagName=='LI'){
            search_input.value=e.target.innerHTML;
            search_autocomplete_btn.click();
          }
        }
      }
      else search_autocomplete_block.style.display='none';
    }
  });
});

search_input.onblur=function()
{
  if(this.value=='') search_autocomplete_block.style.display='none';
}
      </script>

</div>





</main>




<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>