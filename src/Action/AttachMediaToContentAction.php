<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Action;

use Bolt\Exception\InvalidRepositoryException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidContentFieldException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidContentFieldTypeException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Repository\ContentRepository;
use Bolt\Extension\Kryst3q\RestApiContactForm\Uploader\UploadedFile;
use Bolt\Extension\Kryst3q\RestApiContactForm\Uploader\Uploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AttachMediaToContentAction
{
    /**
     * @var ContentRepository
     */
    private $contentRepository;

    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(ContentRepository $contentRepository, Uploader $uploader)
    {
        $this->contentRepository = $contentRepository;
        $this->uploader = $uploader;
    }

    /**
     * @param Request $request
     * @param string $contentType
     * @param int $contentTypeId
     * @return JsonResponse
     * @throws InvalidRepositoryException
     * @throws InvalidContentFieldException
     * @throws InvalidContentFieldTypeException
     */
    public function handle(Request $request, $contentType, $contentTypeId)
    {
        $content = $this->contentRepository->find($contentType, $contentTypeId);
        $uploadedFiles = $this->uploader->upload($contentType, $request->files);

        /** @var UploadedFile $uploadedFile */
        foreach ($uploadedFiles as $uploadedFile) {
            $content->set(
                $uploadedFile->getContentFieldName(),
                $uploadedFile->getPath()
            );
        }

        $this->contentRepository->update($content);

        return new JsonResponse();
    }
}
