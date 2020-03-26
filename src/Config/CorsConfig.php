<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Config;

class CorsConfig
{
    /**
     * @var string
     */
    private $allowedOrigin;

    /**
     * @var string
     */
    private $allowedHeaders;

    /**
     * @var string
     */
    private $allowedMethods;

    /**
     * @param string $allowedOrigin
     * @param string $allowedHeaders
     * @param string $allowedMethods
     */
    public function __construct(
        $allowedOrigin = '*',
        $allowedHeaders = '*',
        $allowedMethods = '*'
    ) {
        $this->allowedOrigin = $allowedOrigin;
        $this->allowedHeaders = $allowedHeaders;
        $this->allowedMethods = $allowedMethods;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Access-Control-Allow-Origin' => $this->allowedOrigin,
            'Access-Control-Allow-Headers' => $this->allowedHeaders,
            'Access-Control-Allow-Methods' => $this->allowedMethods
        ];
    }
}
