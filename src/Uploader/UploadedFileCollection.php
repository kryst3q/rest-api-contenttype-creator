<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Uploader;

use ArrayIterator;
use IteratorAggregate;

class UploadedFileCollection implements IteratorAggregate
{
    /**
     * @var UploadedFile[]
     */
    private $uploadedFiles;

    /**
     * @param UploadedFile[] $uploadedFiles
     */
    public function __construct(array $uploadedFiles = [])
    {
        foreach ($uploadedFiles as $uploadedFile) {
            $this->add($uploadedFile);
        }
    }

    public function add(UploadedFile $uploadedFile)
    {
        $this->uploadedFiles[] = $uploadedFile;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->uploadedFiles);
    }
}
