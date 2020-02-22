<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

class InvalidBodyContentException extends \Exception implements TranslatedException
{
    /**
     * @var string
     */
    private $contentType;

    /**
     * @param string $contentType
     */
    public function __construct($contentType)
    {
        parent::__construct('Request body content is invalid {contentType}.');

        $this->contentType = $contentType;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters()
    {
        return [
            '{contentType}' => $this->contentType
        ];
    }
}