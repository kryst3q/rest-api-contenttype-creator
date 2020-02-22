<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Translator;

use Bolt\Translation\Translator as Trans;

class Translator
{
    /**
     * @param string $key
     * @param array $params
     * @param string $domain
     * @param null $locale
     * @return string
     */
    public function trans($key, $params = [], $domain = 'messages', $locale = null)
    {
        return Trans::__($key, $params, $domain, $locale);
    }
}
