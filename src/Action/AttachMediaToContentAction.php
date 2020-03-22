<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Action;

use Bolt\Exception\InvalidRepositoryException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidContentFieldException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidContentFieldTypeException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Mailer\Mailer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Repository\ContentRepository;
use Bolt\Extension\Kryst3q\RestApiContactForm\Uploader\Uploaded;
use Bolt\Extension\Kryst3q\RestApiContactForm\Uploader\Uploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AttachMediaToContentAction
{
    const NAME = 'attach_media';

    /**
     * @var ContentRepository
     */
    private $contentRepository;

    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(ContentRepository $contentRepository, Uploader $uploader, Mailer $mailer)
    {
        $this->contentRepository = $contentRepository;
        $this->uploader = $uploader;
        $this->mailer = $mailer;
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

        /** @var Uploaded $uploadedFile */
        foreach ($uploadedFiles as $uploadedFile) {
            $content->set(
                $uploadedFile->getContentFieldName(),
                $uploadedFile->getPath()
            );
        }

        $this->contentRepository->update($content);
        $this->mailer->sendEmail($content, self::NAME);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
