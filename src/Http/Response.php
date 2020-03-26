<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Http;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config, $data = null, $status = SymfonyResponse::HTTP_OK, array $headers = [])
    {
        $this->config = $config;

        parent::__construct($data, $status, $this->getHeaders($headers));
    }

    /**
     * @return array
     */
    protected function getHeaders(array $headers)
    {
        return array_merge(
            $this->config->getCorsConfig()->getHeaders(),
            $headers
        );
    }
}
