<?php
/**
 * Created by PhpStorm.
 * User: sofu
 */

namespace Mediway\Dhccam;


class GatewayManager
{
    private $baseUrl;

    private $appId;

    private $token;

    private $signGenerator;

    private $dataMcrypt;

    private $nonceStr = '123456';

    public function __construct($baseUrl, $appId, $token, SignGenerator $signGenerator, AESDataMcrypt $dataMcrypt)
    {
        $this->baseUrl = $baseUrl;
        $this->appId = $appId;
        $this->token = $token;
        $this->signGenerator = $signGenerator;
        $this->dataMcrypt = $dataMcrypt;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param mixed $baseUrl
     */
    public function setBaseUrl($baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getNonceStr(): string
    {
        return $this->nonceStr;
    }

    /**
     * @param string $nonceStr
     */
    public function setNonceStr(string $nonceStr): void
    {
        $this->nonceStr = $nonceStr;
    }


    /**
     * @param $method
     * @param $bizContent
     * @param $version
     * @param $files File[]
     */
    public function post($method, $bizContent, $version, $files = []){
        $url = $this->baseUrl . '/gateway/index';
        $headers = array();

        $data = [
            'token_type'=>'api_credentials',
            'token'=>$this->token,
            'method'=>$method,
            'version'=>$version,
            'app_id'=>$this->appId,
            'nonce_str'=>$this->nonceStr,
            'biz_content'=>$bizContent
        ];

        $sign = $this->signGenerator->sign($data);
        $enBizContent = $this->dataMcrypt->encrypt($bizContent);

        $data['sign'] = $sign;
        $data['biz_content'] = $enBizContent;

        if (!empty($files)) {
            foreach ($files as $file) {
                $data['files'][] = $file->getRequestData();
            }
        }

        return $this->doPost($url, $headers, json_encode($data, JSON_UNESCAPED_UNICODE));

    }

    private function doPost($url, $headers, $body){
        $t1 = microtime(true);
        $ch = curl_init();
        $options = array(
            CURLOPT_USERAGENT => self::userAgent(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_URL => $url,
        );
        // Handle open_basedir & safe mode
        if (!ini_get('safe_mode') && !ini_get('open_basedir')) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
        }
        if (!empty($headers)) {
            $header = array();
            foreach ($headers as $key => $val) {
                array_push($header, "$key: $val");
            }
            $options[CURLOPT_HTTPHEADER] = $header;
        }
        if (!empty($body)) {
            $options[CURLOPT_POSTFIELDS] = $body;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $t2 = microtime(true);
        $duration = round($t2 - $t1, 3);
        $ret = curl_errno($ch);
        if ($ret !== 0) {
            $r = array(-1, $duration, array(), null, curl_error($ch));
            curl_close($ch);
            return $r;
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = self::parseHeaders(substr($result, 0, $header_size));
        $body = substr($result, $header_size);
        curl_close($ch);
        return array($code, $duration, $headers, $body, null);
    }

    private static function userAgent()
    {
        $sdkInfo = "DhccAmPHP/ 1.0";

        $systemInfo = php_uname("s");
        $machineInfo = php_uname("m");

        $envInfo = "($systemInfo/$machineInfo)";

        $phpVer = phpversion();

        $ua = "$sdkInfo $envInfo PHP/$phpVer";
        return $ua;
    }

    private static function parseHeaders($raw)
    {
        $headers = array();
        $headerLines = explode("\r\n", $raw);
        foreach ($headerLines as $line) {
            $headerLine = trim($line);
            $kv = explode(':', $headerLine);
            if (count($kv) > 1) {
                $kv[0] =self::ucwordsHyphen($kv[0]);
                $headers[$kv[0]] = trim($kv[1]);
            }
        }
        return $headers;
    }

    private static function ucwordsHyphen($str)
    {
        return str_replace('- ', '-', ucwords(str_replace('-', '- ', $str)));
    }

}