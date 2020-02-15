<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Provider;

use Bolt\Extension\Kryst3q\RestApiContactForm\Action\IncomingContactFormAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\EmailConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\MessageConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ReceiverConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\SenderConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\DataTransformer\ContactFormDataTransformer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Listener\ExceptionListener;
use Bolt\Extension\Kryst3q\RestApiContactForm\Mailer\Mailer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Storage\Entity\ContactForm;
use Bolt\Extension\Kryst3q\RestApiContactForm\Storage\Repository\ContactFormRepository;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Swift_SmtpTransport;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        $this->registerConfig($app);
        $this->registerExceptionListener($app);
        $this->registerContactFormDataTransformer($app);
        $this->registerContactFormRepository($app);
        $this->registerMailer($app);
        $this->registerIncomingContactFormAction($app);
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }

    /**
     * @param Application $app
     */
    private function registerConfig(Application $app)
    {
        $app[Config::class] = $app->share(
            function () {
                return new Config(
                    $this->config['api_prefix'],
                    new EmailConfig(
                        $this->config['email_configuration']['host'],
                        $this->config['email_configuration']['port'],
                        $this->config['email_configuration']['security'],
                        $this->config['email_configuration']['username'],
                        $this->config['email_configuration']['password']
                    ),
                    new SenderConfig(
                        $this->config['sender']['name'],
                        $this->config['sender']['email']
                    ),
                    new ReceiverConfig(
                        $this->config['receiver']['name'],
                        $this->config['receiver']['email']
                    ),
                    new MessageConfig(
                        $this->config['message']['subject'],
                        $this->config['message']['template']
                    )
                );
            }
        );
    }

    /**
     * @param Application $app
     */
    private function registerExceptionListener(Application $app)
    {
        $app[ExceptionListener::class] = $app->share(
            function () {
                return new ExceptionListener();
            }
        );
    }

    /**
     * @param Application $app
     */
    private function registerContactFormDataTransformer(Application $app)
    {
        $app[ContactFormDataTransformer::class] = $app->share(
            function ($app) {
                return new ContactFormDataTransformer($app['validator']);
            }
        );
    }

    /**
     * @param Application $app
     */
    private function registerContactFormRepository(Application $app)
    {
        $app[ContactFormRepository::class] = $app->share(
            function ($app) {
                return $app['storage']->getRepository(ContactForm::TABLE_NAME);
            }
        );
    }

    /**
     * @param Application $app
     */
    private function registerIncomingContactFormAction(Application $app)
    {
        $app[IncomingContactFormAction::class] = $app->share(
            function ($app) {
                return new IncomingContactFormAction(
                    $app[ContactFormRepository::class],
                    $app[Mailer::class]
                );
            }
        );
    }

    /**
     * @param Application $app
     */
    private function registerMailer(Application $app)
    {
        /** @var EmailConfig $emailConfig */
        $emailConfig = $app[Config::class]->getEmailConfig();
        /*
         * TODO: add handling another forms of transport
         */
        $transport = new Swift_SmtpTransport(
            $emailConfig->getHost(),
            $emailConfig->getPort(),
            $emailConfig->getSecurity()
        );
        $app[Mailer::class] = $app->share(
            function ($app) use ($transport) {
                return new Mailer($app[Config::class], $transport);
            }
        );
    }
}
