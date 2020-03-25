<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm;

use Bolt\Extension\Kryst3q\RestApiContactForm\Action\AttachMediaToContentAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\Action\CreateContentAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\Action\SendCorsOptionsResponseAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Controller\Frontend\ContentController;
use Bolt\Extension\Kryst3q\RestApiContactForm\DataTransformer\RequestDataTransformer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Listener\ExceptionListener;
use Bolt\Extension\Kryst3q\RestApiContactForm\Provider\ServiceProvider;
use Bolt\Extension\SimpleExtension;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RestApiContactFormExtension extends SimpleExtension
{
    /**
     * {@inheritDoc}
     */
    public function getServiceProviders()
    {
        return [
            $this,
            new ServiceProvider($this->getConfig())
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function registerFrontendControllers()
    {
        /** @var Config $config */
        $config = $this->get(Config::class);
        $contentController = new ContentController(
            $this->get(CreateContentAction::class),
            $this->get(AttachMediaToContentAction::class),
            $this->get(SendCorsOptionsResponseAction::class)
        );

        return  [
            $config->getApiPrefix() => $contentController
        ];
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        $parentEvents = parent::getSubscribedEvents();

        $extensionEvents = [
            KernelEvents::EXCEPTION => ['onError', 515],
        ];

        return array_merge($parentEvents, $extensionEvents);
    }

    public function onError(GetResponseForExceptionEvent $event)
    {
        /** @var ExceptionListener $exceptionListener */
        $exceptionListener = $this->get(ExceptionListener::class);
        $response = $exceptionListener->handle($event->getException());
        $event->setResponse($response);
    }

    /**
     * @param $serviceId
     * @return mixed
     */
    private function get($serviceId)
    {
        return $this->getContainer()->offsetGet($serviceId);
    }
}
