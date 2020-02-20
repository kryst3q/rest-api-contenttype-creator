<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

class UnsuccessfulContentSaveException extends \Exception
{
    public function __construct()
    {
        parent::__construct('An error occurred during saving content to database.');
    }
}
