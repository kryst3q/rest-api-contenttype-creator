<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Repository;

use Bolt\Exception\InvalidRepositoryException;
use Bolt\Storage\Entity\Content;
use Bolt\Storage\EntityManager;

class ContentRepository
{
    /**
     * @var EntityManager
     */
    private $storage;

    public function __construct(EntityManager $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param string $contentType
     * @param int $contentId
     * @return Content|null
     * @throws InvalidRepositoryException
     */
    public function find($contentType, $contentId)
    {
        return $this->storage->getRepository($contentType)->find($contentId);
    }

    /**
     * @param Content $content
     * @throws InvalidRepositoryException
     */
    public function update(Content $content)
    {
        $this->storage->getRepository($content->getContenttype())->update($content);
    }
}
