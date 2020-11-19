<?php
/**
 * Created by PhpStorm.
 * User: sofu
 */

namespace Mediway\Dhccam;;


class AESDataMcrypt
{
    private $mcryptKey;
    private $mcryptIv;
    private $mcryptMode;

    public function __construct($mcryptMode, $mcryptKey, $mcryptIv)
    {
        $this->mcryptMode = $mcryptMode;
        $this->mcryptKey = $mcryptKey;
        $this->mcryptIv = $mcryptIv;
    }

    public function encrypt(array $data){
        $raw = json_encode($data, JSON_UNESCAPED_UNICODE);
        $data = openssl_encrypt($raw, $this->mcryptMode , $this->mcryptKey,  OPENSSL_PKCS1_PADDING, $this->mcryptIv);
        return strtoupper(bin2hex($data));
    }

    public function decrypt($data){
        $data = hex2bin($data);
        $raw = openssl_decrypt($data, $this->mcryptMode, $this->mcryptKey,  OPENSSL_PKCS1_PADDING, $this->mcryptIv);
        return $raw;
    }
}