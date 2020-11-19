<?php
/**
 * Created by PhpStorm.
 * User: sofu
 */

namespace Mediway\Dhccam;


class SignGenerator
{
    private $signType;
    private $signKey;

    public function __construct($signType, $signKey)
    {
        $this->signType = $signType;
        $this->signKey = $signKey;
    }

    public function sign(array $data){
        $params = $this->signNormalize($data);
        ksort($params);
        $query = http_build_query($params);
        $query = urldecode($query) . $this->signKey;
        return strtoupper(hash($this->signType, $query, false));
    }

    private function signNormalize(array $params){

        $bizContent = $params['biz_content'];

        $data = array_filter($params, function ($key){
            return in_array($key, ['token_type', 'token', 'method', 'version', 'app_id', 'nonce_str']);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($data as $key => $value){
            if (empty($value) && $value != 0){
                unset($data[$key]);
            }

            if($value === true || $value === false){
                $data[$key] = $value?'true':'false';
            }

            if($value === null){
                $data[$key] = 'null';
            }
        }
        if (!array_key_exists('nonce_str', $params)) {
            $data['nonce_str'] = md5(uniqid(microtime()));
        }

        foreach ($bizContent as $key => $val){
            if (\is_array($val)){
                $bizContent[$key] = json_encode($val, JSON_UNESCAPED_UNICODE);
            }
        }

        return array_merge($data, $bizContent);
    }
}