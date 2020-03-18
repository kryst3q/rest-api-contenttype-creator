<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

use Exception;

class InvalidContentFieldException extends Exception implements TranslatedException
{
    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @param string $contentType
     * @param string $fieldName
     */
    public function __construct($contentType, $fieldName)
    {
        parent::__construct('Content type "{contentType}" has no field named "{fieldName}".');

        $this->contentType = $contentType;
        $this->fieldName = $fieldName;
    }

    /**
     * @inheritDoc
     */
    public function getParameters()
    {
        return [
            '{contentType}' => $this->contentType,
            '{fieldName}' => $this->fieldName
        ];
    }
}
