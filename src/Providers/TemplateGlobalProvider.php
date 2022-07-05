<?php

namespace WakeWorks\RequestProxy\Providers;

use WakeWorks\RequestProxy\RequestProxy;

class TemplateGlobalProvider implements \SilverStripe\View\TemplateGlobalProvider {
    public static function get_template_global_variables() {
        return [
            'RequestProxy' => 'template_data'
        ];
    }

    public static function template_data($key = '') {
        return RequestProxy::get_url($key);
    }
}