<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

class NotHandledContentTypeException extends \DomainException
{
    public function __construct($contentType)
    {
        parent::__construct(sprintf('Content type "%s" is not handled.', $contentType));
    }
}
