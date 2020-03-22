<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Uploader;

use ArrayIterator;
use IteratorAggregate;

class UploadedCollection implements IteratorAggregate
{
    /**
     * @var Uploaded[]
     */
    private $uploaded;

    /**
     * @param Uploaded[] $uploadedFiles
     */
    public function __construct(array $uploadedFiles = [])
    {
        foreach ($uploadedFiles as $uploadedFile) {
            $this->add($uploadedFile);
        }
    }

    public function add(Uploaded $uploaded)
    {
        $this->uploaded[$uploaded->getContentFieldName()][] = $uploaded;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->uploaded);
    }
}
