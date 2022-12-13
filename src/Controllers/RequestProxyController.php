<?php

namespace WakeWorks\RequestProxy\Controllers;

use SilverStripe\Control\Controller;
use WakeWorks\RequestProxy\RequestProxy;
use InvalidArgumentException;

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

        if($curl_response['statusCode'] === 0) {
            // A timeout has occured, set HTTP status code to timeout.
            $curl_response['statusCode'] = 408;
        }

        try {
            $response->setStatusCode(
                isset($target['status_code']) ?
                    $target['status_code'] : $curl_response['statusCode']
            );
        } catch(InvalidArgumentException $_exception) {
            $response->setStatusCode(500);
        }

        $response
            ->addHeader('Content-Type', isset($target['content_type']) ? $target['content_type'] : $curl_response['contentType'])
            ->setBody($curl_response['body']);
        
        $this->extend('updateResponse', $response);

        return $response;
    }

}