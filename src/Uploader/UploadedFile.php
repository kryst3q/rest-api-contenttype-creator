<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Uploader;

class UploadedFile
{
    /**
     * @var string
     */
    private $contentFieldName;

    /**
     * @var string
     */
    private $path;

    public function __construct($contentFieldName, $path)
    {
        $this->contentFieldName = $contentFieldName;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getContentFieldName()
    {
        return $this->contentFieldName;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
