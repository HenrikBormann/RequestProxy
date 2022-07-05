<?php

namespace WakeWorks\RequestProxy\Middlewares;

class RateLimitMiddleware extends \SilverStripe\Control\Middleware\RateLimitMiddleware {
    
    protected function getKeyFromRequest($request) {
        $proxyKey = $request->param('Key') ?? '';

        return md5(parent::getKeyFromRequest($request) . $proxyKey);
    }

}