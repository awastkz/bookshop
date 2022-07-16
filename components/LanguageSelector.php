<?php

namespace app\components;

use Yii;
use yii\base\BootstrapInterface;

class LanguageSelector implements BootstrapInterface
{

    public $supportLang=['en-US','ru-RU'];

    public function bootstrap($app)
    {
        $session=$app->session;
        $session->open();

        if(isset($session['lang']) && in_array($session['lang'],$this->supportLang)){
            $app->language=$session['lang'];
        }
        else $session['lang']=$app->language;
    }
    
    public static function getLangTitle($lang)
    {
        return strtoupper(substr($lang,0,2));
    }

}

?>