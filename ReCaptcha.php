<?php

namespace web24dev\recaptcha;

use yii\base\InvalidConfigException;
use yii\widgets\InputWidget;

class ReCaptcha extends InputWidget
{
    public $siteKey;
    public $secret;

    public function run()
    {
        if (empty($this->siteKey)) {
            $reCaptcha = \Yii::$app->reCaptcha;
            if ($reCaptcha && !empty($reCaptcha->siteKey)) {
                $this->siteKey = $reCaptcha->siteKey;
            } else {
                throw new InvalidConfigException('Required `siteKey` param isn\'t set.');
            }
        }

        $view = $this->view;
        $view->registerJsFile('//www.google.com/recaptcha/api.js?hl=en', ['position' => \yii\web\View::POS_END]);

        return '<div class="g-recaptcha" data-sitekey="' . $this->siteKey . '"></div>';
    }
}
