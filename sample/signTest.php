<?php
/**
 * Created by PhpStorm.
 * User: sofu
 */

require __DIR__. '/../vendor/autoload.php';

use Mediway\Dhccam\SignGenerator;

$signGenerator = new SignGenerator('md5', 'huanzheduan12345');

$data = [
    'token_type'=>'api_credentials',
    'token'=>'LDM1NDUwNDk2NTEsNzMxYTVkZjYsNmFlMTVkMDA0NTI1LTU5YWItOWUxMS00NzQ0LWRmYzE5ODI1LDE1NjkzOTgyNTM=',
    'method'=>'patient.register.patientinfo',
    'version'=>'v1.0',
    'app_id'=>'52891cfd-4474-11e9-ba95-525400d51ea6',
    'nonce_str'=>'123456',
    'biz_content'=>[
        'RequestXML'=>"<Request></Request>"
    ]
];

$sign = $signGenerator->sign($data);

return $sign;
