<?php

namespace WakeWorks\RequestProxy;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;

class RequestProxy {
    use Configurable;
    use Extensible;

    protected static $inst = null;
    
    /**
     * @config
     */
    private static $proxy_rules = [];

    /**
     * @config
     */
    private static $curl_timeout = 10;

    public static function inst()
    {
        if (!self::$inst) {
            self::$inst = new RequestProxy();
        }

        return self::$inst;
    }

    public static function get_proxy_url($key = '') {
        return "/_requestproxy/$key";
    }

    public function make_request($url) {
        $curl = curl_init();
        $this->setCurlOptions($curl, $url);
        return $this->getCurlResponseData($curl);
    }

    public function getProxyRules() {
        return $this->config()->get('proxy_rules');
    }

    private function setCurlOptions($curl, $url) {
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $this->extend('updateCurlOptions', $curl, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->config()->get('curl_timeout'));
    }

    private function getCurlResponseData($curl) {
        $body = curl_exec($curl);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $data = [
            'body' => $body,
            'contentType' => $contentType,
            'statusCode' => $statusCode
        ];

        $this->extend('updateCurlResponseData', $curl, $data);

        curl_close($curl);

        return $data;
    }

}