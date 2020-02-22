<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Controller\Frontend;

use Bolt\Exception\InvalidRepositoryException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Action\IncomingContentTypeFormAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\DataTransformer\RequestDataTransformer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\UnsuccessfulContentTypeSaveException;
use Bolt\Storage\Entity\Content;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactFormController implements ControllerProviderInterface
{
    /**
     * @var RequestDataTransformer
     */
    private $requestDataTransformer;

    /**
     * @var IncomingContentTypeFormAction
     */
    private $incomingContentTypeFormAction;

    public function __construct(
        RequestDataTransformer $requestDataTransformer,
        IncomingContentTypeFormAction $incomingContentTypeFormAction
    ) {
        $this->requestDataTransformer = $requestDataTransformer;
        $this->incomingContentTypeFormAction = $incomingContentTypeFormAction;
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

        $controllerCollection
            ->post('/create/{contentType}', [$this, 'processIncomingContactForm'])
            ->convert('content', [$this->requestDataTransformer, 'transform']);

        return $controllerCollection;
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param string $contentType
     * @param Content $content
     * @return JsonResponse
     * @throws InvalidRepositoryException
     * @throws UnsuccessfulContentTypeSaveException
     */
    public function processIncomingContactForm(
        Application $app,
       Request $request,
       $contentType,
       Content $content
    ) {
        return $this->incomingContentTypeFormAction->handle($content);
    }
}