<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\components\LanguageSelector;

class BaseController extends Controller
{
    
    public function beforeAction($action)
    {
        if(!Yii::$app->user->isGuest)
        {
           $user=User::findOne(Yii::$app->user->id);
           $user->last_active=date('d-m-Y H:i');
           $user->save();
        }
        
      return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionPortfolio()
    {
        return $this->render('portfolio');
    }

    public function actionChangeLang($lang)
    {
        $languageSelector=new LanguageSelector;

        if(in_array($lang,$languageSelector->supportLang)){
            $session=Yii::$app->session;
            $session->open();

            Yii::$app->language=$lang;
            $session['lang']=$lang;
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionError()
    {
        $exception=Yii::$app->errorHandler->exception;

        return $this->render('error',['exception' => $exception]);
    }

}

?>