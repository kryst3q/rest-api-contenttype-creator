<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

class InvalidContentFieldTypeException extends \Exception implements TranslatedException
{
    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $expectedType;

    /**
     * @var string
     */
    private $actualType;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @param string $contentType
     * @param string $fieldName
     * @param string $expectedType
     * @param string $actualType
     */
    public function __construct($contentType, $fieldName, $expectedType, $actualType)
    {
        parent::__construct('Field "{fieldName}" of content type "{contentType}" is of type {actualType} but {expectedType} expected.');

        $this->contentType = $contentType;
        $this->expectedType = $expectedType;
        $this->actualType = $actualType;
        $this->fieldName = $fieldName;
    }

    /**
     * @inheritDoc
     */
    public function getParameters()
    {
        return [
            '{fieldName}' => $this->fieldName,
            '{contentType}' => $this->contentType,
            '{actualType}' => $this->actualType,
            '{expectedType}' => $this->expectedType
        ];
    }
}
