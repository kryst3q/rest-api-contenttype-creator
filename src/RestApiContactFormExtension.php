<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm;

use Bolt\Extension\Kryst3q\RestApiContactForm\Controller\Frontend\ContactFormController;
use Bolt\Extension\Kryst3q\RestApiContactForm\Listener\ExceptionListener;
use Bolt\Extension\Kryst3q\RestApiContactForm\Provider\ServiceProvider;
use Bolt\Extension\Kryst3q\RestApiContactForm\Storage\Entity\ContactForm;
use Bolt\Extension\Kryst3q\RestApiContactForm\Storage\Repository\ContactFormRepository;
use Bolt\Extension\SimpleExtension;
use Silex\Application;
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
        return  [
            '/api' => new ContactFormController()
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices(Application $app)
    {
        $this->extendRepositoryMapping();
    }

    /**
     * {@inheritdoc}
     */
    protected function registerRepositoryMappings()
    {
        return [
            ContactForm::TABLE_NAME => [ContactForm::class => ContactFormRepository::class],
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
        $exceptionListener = $this->getContainer()->offsetGet(ExceptionListener::class);
        $response = $exceptionListener->handle($event->getException());
        $event->setResponse($response);
    }
}
