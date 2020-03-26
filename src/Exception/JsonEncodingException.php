<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

class JsonEncodingException extends \Exception implements TranslatedException
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        parent::__construct('An error occurred during encoding data to JSON format. Tried to encode {data}');
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function getParameters()
    {
        return [
            '{data}' => var_export($this->data, true)
        ];
    }
}
