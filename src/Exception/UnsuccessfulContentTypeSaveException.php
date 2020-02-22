<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

class UnsuccessfulContentTypeSaveException extends \Exception implements TranslatedException
{
    public function __construct()
    {
        parent::__construct('An error occurred during saving received content type.');
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters()
    {
        return [];
    }
}
