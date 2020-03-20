<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

class InvalidFileExtensionException extends \Exception implements TranslatedException
{
    /**
     * @var string
     */
    private $invalidFileExtension;

    /**
     * @var array
     */
    private $validFileExtensions;

    public function __construct($invalidFileExtension, array $validFileExtensions)
    {
        parent::__construct(
            'Extension {invalidFileExtension} is not accepted. Valid file extensions are {validFileExtensions}.'
        );

        $this->invalidFileExtension = $invalidFileExtension;
        $this->validFileExtensions = $validFileExtensions;
    }

    /**
     * @inheritDoc
     */
    public function getParameters()
    {
        return [
            '{invalidFileExtension}' => $this->invalidFileExtension,
            '{validFileExtensions}' => implode(', ', $this->validFileExtensions)
        ];
    }
}