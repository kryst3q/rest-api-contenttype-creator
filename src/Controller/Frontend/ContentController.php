<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Controller\Frontend;

use Bolt\Exception\InvalidRepositoryException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Action\AttachMediaToContentAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\Action\CreateContentAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\Action\SendCorsOptionsResponseAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidArgumentException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidBodyContentException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidContentFieldException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidContentFieldTypeException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\UnsuccessfulContentTypeSaveException;
use Bolt\Storage\Entity\Content;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentController implements ControllerProviderInterface
{
    /**
     * @var CreateContentAction
     */
    private $createContentAction;

    /**
     * @var AttachMediaToContentAction
     */
    private $attachMediaToContentAction;

    /**
     * @var SendCorsOptionsResponseAction
     */
    private $sendCorsOptionsResponseAction;

    public function __construct(
        CreateContentAction $incomingContentTypeFormAction,
        AttachMediaToContentAction $attachMediaToContentAction,
        SendCorsOptionsResponseAction $sendCorsOptionsResponseAction
    ) {
        $this->createContentAction = $incomingContentTypeFormAction;
        $this->attachMediaToContentAction = $attachMediaToContentAction;
        $this->sendCorsOptionsResponseAction = $sendCorsOptionsResponseAction;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(Application $app)
    {
        /**
         * @var $controllerCollection ControllerCollection
         */
        $controllerCollection = $app['controllers_factory'];

        $controllerCollection->options('/create/{contentType}', [$this, 'sendCorsOptionsResponse']);
        $controllerCollection->options('/media/{contentType}/{contentTypeId}', [$this, 'sendCorsOptionsResponse']);
        $controllerCollection->post('/create/{contentType}', [$this, 'createContent']);
        $controllerCollection->post('/media/{contentType}/{contentTypeId}', [$this, 'attachMediaToContent']);

        return $controllerCollection;
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param string $contentType
     *
     * @return JsonResponse
     *
     * @throws InvalidRepositoryException
     * @throws UnsuccessfulContentTypeSaveException
     * @throws InvalidArgumentException
     * @throws InvalidBodyContentException
     */
    public function createContent(Application $app, Request $request, $contentType)
    {
        return $this->createContentAction->perform($contentType, $request);
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param string $contentType
     * @param int $contentTypeId
     *
     * @return JsonResponse
     *
     * @throws InvalidRepositoryException
     * @throws InvalidContentFieldException
     * @throws InvalidContentFieldTypeException
     */
    public function attachMediaToContent(Application $app, Request $request, $contentType, $contentTypeId)
    {
        return $this->attachMediaToContentAction->perform($request, $contentType, $contentTypeId);
    }

    /**
     * @return Response
     */
    public function sendCorsOptionsResponse()
    {
        return $this->sendCorsOptionsResponseAction->perform();
    }
}