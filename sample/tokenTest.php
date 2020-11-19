<?php
/**
 * Created by PhpStorm.
 * User: sofu
 */

use Mediway\Dhccam\Auth;

require __DIR__. '/../vendor/autoload.php';

$url = 'http://localhost:9005';
$appId = 'bb1ee6a5-6a36-11e8-9f51-00163e036ce4';
$appKey = '893fd016ca376770de40f3a361809e52';

$auth = new Auth($url, $appId, $appKey);

list($code, $duration, $header,$body, $error) = $auth->getToken();

echo $body;