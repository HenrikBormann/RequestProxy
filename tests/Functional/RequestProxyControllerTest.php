<?php

use SilverStripe\Dev\FunctionalTest;
use WakeWorks\RequestProxy\RequestProxy;

class RequestProxyControllerTest extends FunctionalTest {

    private $proxy_rules = [
        'works' => 'http://example.com/',
        'notfound' => 'https://google.com/asdf',
        'exception' => 'http://127.0.0.1:9999',
        'forcecss' => [
            'url' => 'http://example.com',
            'content_type' => 'text/css'
        ],
        'forcestatus' => [
            'url' => 'https://google.com',
            'status_code' => 418
        ]
    ];

    protected function setUp(): void {
        parent::setUp();

        RequestProxy::config()->set('curl_timeout', 3);
        RequestProxy::config()->set('proxy_rules', $this->proxy_rules);
    }

    public function getResultByKey($key) {
        $url = RequestProxy::get_proxy_url($key);
        return $this->get($url);
    }

    public function testUnknownKey() {
        $unknownKey = 'probablyUnknownIDK';

        $result = $this->getResultByKey($unknownKey);
        $this->assertEquals(404, $result->getStatusCode());
    }

    public function testWorkingKey() {
        $result = $this->getResultByKey('works');
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString(
            'Example Domain',
            $result->getBody()
        );
    }

    public function testNotFound() {
        $result = $this->getResultByKey('notfound');
        $this->assertEquals(404, $result->getStatusCode());
    }

    public function testException() {
        $result = $this->getResultByKey('exception');
        $this->assertEquals(408, $result->getStatusCode());
    }

    public function testForceContentType() {
        $result = $this->getResultByKey('forcecss');
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(
            $this->proxy_rules['forcecss']['content_type'],
            $result->getHeader('content-type')
        );
    }

    public function testForceStatusCode() {
        $result = $this->getResultByKey('forcestatus');
        $this->assertEquals(
            $this->proxy_rules['forcestatus']['status_code'],
            $result->getStatusCode()
        );
    }
}