<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Action;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\CorsConfig;
use Symfony\Component\HttpFoundation\Response;

class SendCorsOptionsResponseAction
{
    /**
     * @var CorsConfig
     */
    private $corsConfig;

    public function __construct(CorsConfig $corsConfig)
    {
        $this->corsConfig = $corsConfig;
    }

    public function perform()
    {
        $response = new Response();

        $response->headers->set(
            'Access-Control-Allow-Origin',
            $this->corsConfig->getAllowedOrigin()
        );

        $response->headers->set(
            'Access-Control-Allow-Headers',
            '*'
        );

        $response->headers->set(
            'Access-Control-Allow-Methods',
            'POST'
        );

        return $response;
    }
}
