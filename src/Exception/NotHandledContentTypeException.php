<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

class NotHandledContentTypeException extends \DomainException implements TranslatedException
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
        parent::__construct('Content type {contentType} is not handled.');

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
