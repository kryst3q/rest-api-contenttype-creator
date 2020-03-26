<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Http;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\JsonEncodingException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class JsonResponse extends Response
{
    /**
     * @param Config $config
     * @param array $data
     * @param int $status
     * @param array $headers
     *
     * @throws JsonEncodingException
     */
    public function __construct(
        Config $config,
        array $data = [],
        $status = SymfonyResponse::HTTP_OK,
        array $headers = []
    ) {
        parent::__construct($config, $this->encodeData($data), $status, $this->getHeaders($headers));
    }

    protected function getHeaders(array $headers)
    {
        return array_merge(
            parent::getHeaders($headers),
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * @return string
     *
     * @throws JsonEncodingException
     */
    private function encodeData(array $data)
    {
        $encodedData = json_encode($data);

        if ($encodedData === false) {
            throw new JsonEncodingException($data);
        }

        return $encodedData;
    }
}
