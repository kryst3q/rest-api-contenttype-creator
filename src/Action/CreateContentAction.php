<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Action;

use Bolt\Exception\InvalidRepositoryException;
use Bolt\Extension\Kryst3q\RestApiContactForm\DataTransformer\RequestDataTransformer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidArgumentException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidBodyContentException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\UnsuccessfulContentTypeSaveException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Mailer\Mailer;
use Bolt\Storage\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateContentAction
{
    const NAME = 'create_content';

    /**
     * @var EntityManager
     */
    private $storage;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var RequestDataTransformer
     */
    private $requestDataTransformer;

    public function __construct(
        EntityManager $storage,
        Mailer $mailer,
        RequestDataTransformer $requestDataTransformer
    ) {
        $this->storage = $storage;
        $this->mailer = $mailer;
        $this->requestDataTransformer = $requestDataTransformer;
    }

    /**
     * @param string $contentType
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws InvalidRepositoryException
     * @throws UnsuccessfulContentTypeSaveException
     * @throws InvalidArgumentException
     * @throws InvalidBodyContentException
     */
    public function perform($contentType, Request $request)
    {
        $content = $this->requestDataTransformer->transform($contentType, $request);
        $repository = $this->storage->getRepository($content->getContenttype());
        $success = $repository->save($content);

        if (!$success) {
            throw new UnsuccessfulContentTypeSaveException();
        }

        $this->mailer->sendEmail($content, self::NAME);

        return new JsonResponse(['id' => $content->getId()], Response::HTTP_OK);
    }
}
