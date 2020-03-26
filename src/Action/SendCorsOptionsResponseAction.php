<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Action;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Http\Response;

class SendCorsOptionsResponseAction
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Response
     */
    public function perform()
    {
        return new Response($this->config);
    }
}
