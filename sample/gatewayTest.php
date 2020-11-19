<?php
/**
 * Created by PhpStorm.
 * User: sofu
 */
require __DIR__. '/../vendor/autoload.php';

const DEFAULT_JSON_FLAG = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;

use Mediway\Dhccam\AESDataMcrypt;
use Mediway\Dhccam\GatewayManager;
use Mediway\Dhccam\SignGenerator;

$url = 'http://localhost:9005';
$appId = '52891cfd-4474-11e9-ba95-525400d51ea6';
$token = 'LDk4OTAyNDk2NTEsNzMxYTVkZjYsNmFlMTVkMDA0NTI1LTU5YWItOWUxMS00NzQ0LWRmYzE5ODI1LDE1Njk0MTM3ODk=';
$signType = 'md5';
$signKey = 'huanzheduan12345';
$encryptKey = 'huanzheduan12345';

$method = 'patient.hospital.rest.test';
$version = 'v1.0';

$signGenerator = new SignGenerator($signType, $signKey);
$mcrypt = new AESDataMcrypt('aes-128-cbc', $encryptKey, '0102030405060708');
$msgr = new GatewayManager($url, $appId, $token, $signGenerator, $mcrypt);

$bizContent = [
    "name" => "hello"
];

list($code, $duration, $headers, $body) = $msgr->post($method, $bizContent, $version);

echo $body;
$data = json_decode($body, true)['data'] ?? '';
// 解密
if ($data) {
    echo PHP_EOL . PHP_EOL;
    $data = $mcrypt->decrypt($data);
    $parseData = json_decode($data, true);
    if (json_last_error()) {
        echo $data;
    } else {
        echo json_encode($parseData, DEFAULT_JSON_FLAG);
    }
}


