<?php

namespace web24dev\recaptcha;

use GuzzleHttp\Client;

use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\validators\Validator;
use yii\base\Exception;

class ReCaptchaValidator extends Validator
{
    public $secret;

    public function init()
    {
        parent::init();

        if (empty($this->secret)) {
            $reCaptcha = \Yii::$app->reCaptcha;
            if ($reCaptcha && !empty($reCaptcha->secret)) {
                $this->secret = $reCaptcha->secret;
            } else {
                throw new InvalidConfigException('Required `secret` param isn\'t set.');
            }
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $result = $this->getResponse($_REQUEST['g-recaptcha-response']);
        if (!isset($result['success'])) {
            throw new Exception('Invalid recaptcha verify response.');
        }

        if ($result['success'] == 0) {
            $this->addError($model, $attribute, 'The "{attribute}" is incorrect.');
        }
    }

    protected function getResponse($value)
    {
        $client = new Client(['verify' => false]);
        $response = $client->request('POST', 'https://google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => $this->secret,
                'response' => $value,
                'remoteip' => \Yii::$app->request->userIP
            ]]);

        if ($response->getReasonPhrase() != 'OK') {
            throw new Exception('Unable connection to the captcha server. Status code ' . $response->statusCode);
        }

        return Json::decode($response->getBody()->getContents());
    }
}