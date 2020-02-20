<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Controller\Frontend;

use Bolt\Extension\Kryst3q\RestApiContactForm\Action\IncomingContentTypeFormAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\DataTransformer\RequestDataTransformer;
use Bolt\Storage\Entity\Content;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactFormController implements ControllerProviderInterface
{
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
            ->convert('content', [$app[RequestDataTransformer::class], 'transform']);

        return $controllerCollection;
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param string $contentType
     * @param Content $content
     * @return JsonResponse
     */
    public function processIncomingContactForm(Application $app, Request $request, $contentType, Content $content)
    {
        return $app[IncomingContentTypeFormAction::class]->handle($content);
    }
}