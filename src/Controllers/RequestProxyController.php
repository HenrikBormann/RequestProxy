<?php

namespace WakeWorks\RequestProxy\Controllers;

use SilverStripe\Control\Controller;
use WakeWorks\RequestProxy\RequestProxy;

class RequestProxyController extends Controller {

    public function index() {
        $key = $this->getRequest()->param('Key');
        $proxyRules = RequestProxy::inst()->getProxyRules();

        if(!is_array($proxyRules) || !isset($proxyRules[$key])) {
            return $this->httpError(404);
        }

        $target = is_array($proxyRules[$key]) ? $proxyRules[$key] : ['url' => $proxyRules[$key]];

        $curl_response = RequestProxy::inst()->make_request($target['url']);

        $response = $this->getResponse();

        $response
            ->setStatusCode(isset($target['status_code']) ? $target['status_code'] : $curl_response['statusCode'])
            ->addHeader('Content-Type', isset($target['content_type']) ? $target['content_type'] : $curl_response['contentType'])
            ->setBody($curl_response['body']);
        
        $this->extend('updateResponse', $response);

        return $response;
    }

}