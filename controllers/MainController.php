<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;

use app\models\User;
use app\models\Login;
use app\models\Register;
use app\models\ForgetPassword;
use app\models\ResetPassword;
use app\models\UserAvatar;
use app\models\Product;
use app\models\UserProfile;
use app\models\HelperModel;
use app\models\Category;
use app\models\Favorites;
use app\models\ProductSort;
use app\models\ProductFilter;
use app\models\Elastic;
use app\models\Search;
use app\models\UploadForm;

use app\components\AuthHandler;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\helpers\Helper;

class MainController extends BaseController
{

public function behaviors()
{
    return [
        'access' => [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index','logout','profile','reset-filter','remove-avatar','search','search-product'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['captcha','search','login','register','index','forget-password',
                                  'reset-password','confirm','reset-filter','auth','search-product'],
                    'roles' => ['?'],
                ],

            ],
        ],

    ];
}

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }


    public function onAuthSuccess($client)
    {

        (new AuthHandler($client))->handle();
    }


public function actionIndex()
{

 $search_model=new Search;
 $product_model=new Product;
 $favorites_model=new Favorites;
 $category=new Category;
 $productFilter_model=new ProductFilter;

 

 $session=Yii::$app->session;
 $session->open();

 $loginModel=new Login;

 if($loginModel->load(Yii::$app->request->post()) && Yii::$app->request->isAjax){
    Yii::$app->response->format=Response::FORMAT_JSON;
    if($loginModel->validate()){
     try{
            $loginModel->userLogin($loginModel);
            return $this->redirect(['main/profile']);
     }
     catch(\DomainException $e){
         return Yii::$app->session->setFlash('error',$e->getMessage());
     }
   }
   else return ActiveForm::validate($loginModel);
 }

 
    if(!Yii::$app->request->get('filter')) $productFilter_model->removeFilterProductSession($session);
        $query=Product::find();
        $sort=Yii::$app->request->get('sort') ? Yii::$app->request->get('sort') : null;

    if($productFilter_model->load(Yii::$app->request->post()) && $productFilter_model->validate()){
        try{
            $productFilter_model->setFilterProductSession($session);
            return $this->redirect(Url::current(['filter' => 'true']));
        }
        catch(\DomainException $e){
            $productFilter_model->removeFilterProductSession($session);
            Yii::$app->session->setFlash('error',$e->getMessage());
            return $this->refresh();
        }
    }

        if(ProductFilter::hasFilterSession($session) && Yii::$app->request->get('filter')){
            $query=$session['filter.query'];
            Yii::$app->session->setFlash('success',$productFilter_model->filterProductText($session['filter.model']));
        }


    return $this->render('index',[
        'products' => HelperModel::getDataPagination($query,Product::PAGE_SIZE,$sort ? $sort : ''),
        'discountProducts' => $product_model->getDiscountProducts(),
        'saleProducts' => $product_model->getSaleProducts(),
        'pages' => HelperModel::getPagePagination($query,Product::PAGE_SIZE),
        'favorites_items' => $favorites_model->getFavoritesData(),
        'favorites_model' => $favorites_model,
        'productFilter_model' => $productFilter_model,
        'session' => $session,
        'product_model' => $product_model,
        'credit_items' => $product_model->creditItems(),
    ]);

}

public function actionResetFilter()
{
    $session=Yii::$app->session;
    $session->open();
    (new ProductFilter)->removeFilterProductSession($session);

    return $this->redirect(Helper::removeUrlGetItems(Yii::$app->request->referrer,'id'));
}

public function actionSearch()
{
    $elastic=new Elastic;

    if(Yii::$app->request->isAjax){

        Yii::$app->response->format=Response::FORMAT_JSON;
        $keyword=Yii::$app->request->get('text');

        $suggestList=$elastic->searchSuggest($keyword);

        return ['data' => $suggestList];
    }
}

public function actionSearchProduct()
{
    $search_model=new Search;
    $products=[];

    if($search_model->load(Yii::$app->request->post())){
        $products=$search_model->search();
    }

    $search_model=new Search;
    $product_model=new Product;
    $favorites_model=new Favorites;
    $category=new Category;
    $productFilter_model=new ProductFilter;
    
    return $this->render('search-product',[
        'products' => $products,
        'favorites_items' => (new Favorites)->getFavoritesData(),
        'favorites_model' => (new Favorites),
        'productFilter_model' => (new ProductFilter),
        'product_model' => (new Product),
        'credit_items' => (new Product)->creditItems(),
    ]);
}

public function actionRegister()
{
    $registerModel=new Register;

    if($registerModel->load(Yii::$app->request->post()) && $registerModel->validate()){
        try{
            $user=$registerModel->registerUser();
            $session->setFlash('success','Вы успешно зарегистрировались подтвердите почту');
            $registerModel->sendMsgConfirmEmail($user);
            return $this->goHome();
         }
         catch(\RuntimeException $e){
             Yii::$app->session->setFlash('error',$e->getMessage());
         }
    }

    return $this->render('register',[
        'model' => $registerModel,
    ]);
}

public function actionConfirm($token)
{
    try{
        $registerModel=new Register;
        $registerModel->confirmEmail($token);
        Yii::$app->session->setFlash('success','Вы успешно подтвердили почту');
    }
    catch(\Exception $e){
        Yii::$app->session->setFlash('error',$e->getMessage());
    }

    return $this->render('confirm');
}

public function actionForgetPassword()
{
    $model=new ForgetPassword;

    if($model->load(Yii::$app->request->post()) && $model->validate()){
         try{
             $user=$model->setResetToken();
             $model->sendMsgResetPassword($user);
             Yii::$app->session->setFlash('success','Вам на почту отправили сообщение о восстановление пароля');
         }
         catch(\Exception $e){
             Yii::$app->session->setFlash('error',$e->getMessage());
         }
    }

    return $this->render('forget-password',compact('model'));
}

public function actionResetPassword($token)
{
    $model=new ResetPassword;

    try{
        $user=$model->passwordRecovery($token);
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $model->setRecoveryPassword($user);
            Yii::$app->session->setFlash('success','Пароль успешно изменен');
        }
    }
    catch(\Exception $e){
        Yii::$app->session->setFlash('error',$e->getMessage());
    }

     return $this->render('reset-password',compact('model'));
}

public function actionProfile()
{
    $model=User::findOne(Yii::$app->user->id);

    $avatarModel=new UserAvatar;
    if($avatarModel->load(Yii::$app->request->post())){
        try{
            $avatarModel->uploadUserAvatar();
            Yii::$app->session->setFlash('success','Аватар успешно изменен');
            return $this->refresh();
        }
        catch(\Exception $e){
            Yii::$app->session->setFlash('error',$e->getMessage());
        }
    }

    $userProfile=new UserProfile;
    if($userProfile->load(Yii::$app->request->post()) && $userProfile->validate()){
        try{
            $userProfile->editUserData();
            Yii::$app->session->setFlash('success','Данные изменены');
            return $this->refresh();
        }
        catch(\RuntimeException $e){
            Yii::$app->session->setFlash('error',$e->getMessage());
        }
    }
    
    return $this->render('profile',[
        'model' => $model,
        'avatarModel' => $avatarModel,
        'userInfo' => $userProfile->getUserInfo($model),
        'userProfile' => $userProfile,
    ]);
}

public function actionRemoveAvatar($id)
{
    $user=User::findOne($id);
    $user_avatar=new UserAvatar;

    if($user_avatar->deleteAvatar($user->avatar) && $user_avatar->setDefaultAvatar($user->id)){
        Yii::$app->session->setFlash('success','Аватар успешно удален');
    }
    else Yii::$app->session->setFlash('error','Произошла ошибка');
    
    return $this->redirect(['main/profile']);
}


public function actionLogout()
{
    Yii::$app->user->logout();
    return $this->goHome();
}


}

?>