# RequestProxy for Silverstripe 4

![Packagist Version](https://img.shields.io/packagist/v/wakeworks/requestproxy?style=flat-square)
![GitHub](https://img.shields.io/github/license/wakeworks/RequestProxy?style=flat-square)

## Introduction
---

RequestProxy allows you to route a user's request through your server. 

Imagine you want to use fonts from Adobe Typekit but your data protection officer tells you: "No communication to US companies under GDPR". Adobe doesn't allow self-hosting, so you can't use it normally. However, you could proxy the user's request through your server and return the font to the user without revealing their IP address.

## Requirements
---

* silverstripe/framework ^4.0
* php-curl extension

## Installation
---

```
composer require wakeworks/requestproxy
```

## Basic Configuration
---
```yaml
WakeWorks\RequestProxy\RequestProxy:
  proxy_rules:
    link1: 'https://external/link/to/proxy'
    link2: 'https://external/link/to/proxy2'
```

Your links will now be available under `/_requestproxy/link1` and `/_requestproxy/link2`.

## Usage
---

### Templates

In Templates, use `$RequestProxy(link1)` to get the link

### Requirements

```php
use SilverStripe\View\Requirements;
use WakeWorks\RequestProxy\RequestProxy;

$proxy_url = RequestProxy::get_url('link1');
Requirements::css($proxy_url);
```

## Advanced Configuration
---

Normally, RequestProxy will forward the Content-Type header and the returned HTTP status code. You can, however, force other ones.

```yaml
WakeWorks\RequestProxy\RequestProxy:
  proxy_rules:
    link3:
      url: 'https://external/link/to/proxy'
      content_type: 'text/css'
      status_code: 201
```

## Rate Limiting
---

Proxying requests can be dangerous, e.g. users could in theory make you DDoS the target service.

RequestProxy implements Silverstripe's RateLimitMiddleware to counter that. It allows 10 requests per link per minute as default value. If you want to change that value, you can change its configuration:

```yaml
---
Name: ratelimit
After:
  - 'requestproxyratelimit'
---
SilverStripe\Core\Injector\Injector:
  WakeWorks\RequestProxy\RateLimitMiddleware:
    class: WakeWorks\RequestProxy\Middlewares\RateLimitMiddleware
    properties:
      ExtraKey: 'requestproxylimiter'
      MaxAttempts: 10
      Decay: 1
```

## Extension Hooks

There are some extension hooks provided.

* `updateResponse($response)` in `WakeWorks\RequestProxy\Controllers\RequestProxyController` allows you to change the response of the default /_requestproxy controller.
* `updateCurlOptions($curl)` in `WakeWorks\RequestProxy\RequestProxy` allows you to add more or change options in the $curl object.
* `updateCurlResponseData($curl, $data)` allows you to add data from $curl into $data that is returned by `RequestProxy::make_request`