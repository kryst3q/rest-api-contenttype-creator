<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Config;

class CorsConfig
{
    /**
     * @var string
     */
    private $allowOrigin;

    /**
     * @param string $allowOrigin
     */
    public function __construct($allowOrigin)
    {
        $this->allowOrigin = $allowOrigin;
    }

    /**
     * @return string
     */
    public function getAllowedOrigin()
    {
        return $this->allowOrigin;
    }
}
