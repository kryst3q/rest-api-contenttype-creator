<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Controller\Frontend;

use Bolt\Extension\Kryst3q\RestApiContactForm\Action\IncomingContactFormAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\DataTransformer\ContactFormDataTransformer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Storage\Entity\ContactForm;
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
            ->post('/contact', [$this, 'processIncomingContactForm'])
            ->convert('contactForm', ContactFormDataTransformer::class.':transform');

        return $controllerCollection;
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param ContactForm $contactForm
     * @return JsonResponse
     */
    public function processIncomingContactForm(Application $app, Request $request, ContactForm $contactForm)
    {
        return $app[IncomingContactFormAction::class]->handle($contactForm);
    }
}