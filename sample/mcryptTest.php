<?php
/**
 * Created by PhpStorm.
 * User: sofu
 */

require __DIR__. '/../vendor/autoload.php';

use Mediway\Dhccam\AESDataMcrypt;

$bizContent = '2a914bc97ccbbc7baea0132a6a9260b4b26e5478b5c372c0b5f1ca43012f0f5d53d1fc9505852b5221864aaa95dafdc8';

$aesMcrypt = new AESDataMcrypt('aes-128-cbc', 'huanzheduan12345', '0102030405060708');

$raw = $aesMcrypt->decrypt($bizContent);

return 'ok';