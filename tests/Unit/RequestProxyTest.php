<?php

use SilverStripe\Dev\SapphireTest;
use WakeWorks\RequestProxy\RequestProxy;

class RequestProxyTest extends SapphireTest {
    
    private $composerJsonLink = 'https://raw.githubusercontent.com/wakeworks/RequestProxy/main/composer.json';

    public function testGetProxyUrl() {
        $someKey = 'abcDEF123-_';

        $this->assertEquals(
            "/_requestproxy/$someKey",
            RequestProxy::get_proxy_url($someKey)
        );
    }

    public function testMakeRequest() {
        $response = RequestProxy::inst()
            ->make_request($this->composerJsonLink);

        $this->assertStringContainsString(
            '"name": "wakeworks/requestproxy"',
            $response['body']
        );
    }
}