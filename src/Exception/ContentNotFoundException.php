<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

class ContentNotFoundException extends \Exception implements TranslatedException
{
    /**
     * @var string
     */
    private $contentId;

    public function __construct($contentId)
    {
        parent::__construct('Content type with id {contentId} not found.');

        $this->contentId = $contentId;
    }

    /**
     * @inheritDoc
     */
    public function getParameters()
    {
        return [
            '{contentId}' => $this->contentId
        ];
    }
}
