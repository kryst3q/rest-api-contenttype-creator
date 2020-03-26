<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Provider;

use Bolt\Extension\Kryst3q\RestApiContactForm\Action\AttachMediaToContentAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\Action\CreateContentAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ContentType;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\EmailConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\MessageConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ReceiverConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\SenderConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\DataTransformer\RequestDataTransformer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Factory\ContentConstraintsFactory;
use Bolt\Extension\Kryst3q\RestApiContactForm\Listener\CorsListener;
use Bolt\Extension\Kryst3q\RestApiContactForm\Listener\ExceptionListener;
use Bolt\Extension\Kryst3q\RestApiContactForm\Mailer\Mailer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Repository\ContentRepository;
use Bolt\Extension\Kryst3q\RestApiContactForm\Translator\Translator;
use Bolt\Extension\Kryst3q\RestApiContactForm\Uploader\Uploader;
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
        $this->registerTranslator($app);
        $this->registerCorsListener($app);
        $this->registerExceptionListener($app);
        $this->registerMailer($app);
        $this->registerContentTypeValidatorConstraintsFactory($app);
        $this->registerRequestDataTransformer($app);
        $this->registerContentRepository($app);
        $this->registerUploader($app);
        $this->registerCreateContentAction($app);
        $this->registerAttachMediaToContentAction($app);
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }

    private function registerConfig(Application $app)
    {
        $config = new Config($this->config['api_prefix']);
        $this->prepareEmailConfigs($config);
        $this->prepareSenderConfigs($config);
        $this->prepareReceiverConfigs($config);
        $this->prepareMessageConfigs($config);
        $this->prepareContentTypes($app, $config);

        $app[Config::class] = $app->share(
            function () use ($config) {
                return $config;
            }
        );
    }

    private function registerExceptionListener(Application $app)
    {
        $app[ExceptionListener::class] = $app->share(
            function ($app) {
                return new ExceptionListener($app[Translator::class]);
            }
        );
    }

    private function registerCreateContentAction(Application $app)
    {
        $app[CreateContentAction::class] = $app->share(
            function ($app) {
                return new CreateContentAction(
                    $app['storage'],
                    $app[Mailer::class],
                    $app[RequestDataTransformer::class]
                );
            }
        );
    }

    private function registerMailer(Application $app)
    {
        /** @var EmailConfig $emailConfig */
        $emailConfig = $app[Config::class]->getEmailConfig(Config::DEFAULT_CONFIG_NAME);

        $transport = new Swift_SmtpTransport(
            $emailConfig->getHost(),
            $emailConfig->getPort(),
            $emailConfig->getSecurity()
        );
        $transport->setUsername($emailConfig->getUsername());
        $transport->setPassword($emailConfig->getPassword());

        $app[Mailer::class] = $app->share(
            function ($app) use ($transport) {
                return new Mailer(
                    $app[Config::class],
                    $transport,
                    $app['filesystem']
                );
            }
        );
    }

    private function prepareEmailConfigs(Config $config)
    {
        foreach ($this->config['email_configuration'] as $name => $data) {
            $emailConfig = new EmailConfig(
                $this->config['email_configuration'][$name]['host'],
                $this->config['email_configuration'][$name]['port'],
                $this->config['email_configuration'][$name]['security'],
                $this->config['email_configuration'][$name]['username'],
                $this->config['email_configuration'][$name]['password']
            );
            $config->addEmailConfig($name, $emailConfig);
        }
    }

    private function prepareSenderConfigs(Config $config)
    {
        foreach ($this->config['sender'] as $name => $data) {
            $senderConfig = new SenderConfig(
                $this->config['sender'][$name]['name'],
                $this->config['sender'][$name]['email']
            );
            $config->addSenderConfig($name, $senderConfig);
        }
    }

    private function prepareReceiverConfigs(Config $config)
    {
        foreach ($this->config['receiver'] as $name => $data) {
            $receiverConfig = new ReceiverConfig(
                $this->config['receiver'][$name]['name'],
                $this->config['receiver'][$name]['email']
            );
            $config->addReceiverConfig($name, $receiverConfig);
        }
    }

    private function prepareMessageConfigs(Config $config)
    {
        foreach ($this->config['message'] as $name => $data) {
            $messageConfig = new MessageConfig(
                $this->config['message'][$name]['subject'],
                $this->config['message'][$name]['template']
            );
            $config->addMessageConfig($name, $messageConfig);
        }
    }

    /**
     * @param string $contentTypeName
     * @param string $configName
     * @param array|string|null $defaultValue
     * @return array|string|null
     */
    private function getContentTypeConfigValue($contentTypeName, $configName, $defaultValue = null)
    {
        return isset($this->config['content_type'][$contentTypeName][$configName])
            ? $this->config['content_type'][$contentTypeName][$configName]
            : $defaultValue;
    }

    private function prepareContentTypes(Application $app, Config $config)
    {
        $availableContentTypes = $app['config']->get('contenttypes');
        $availableContentTypesNames = array_keys($availableContentTypes);

        foreach (array_keys($this->config['content_type']) as $contentTypeName) {
            if (in_array($contentTypeName, $availableContentTypesNames)) {
                $contentType = new ContentType(
                    $contentTypeName,
                    $availableContentTypes[$contentTypeName]['fields'],
                    $this->getContentTypeConfigValue($contentTypeName, 'send_email', null) === true,
                    $this->getContentTypeConfigValue($contentTypeName, 'send_email_after', CreateContentAction::NAME),
                    $this->getContentTypeConfigValue($contentTypeName, 'message_fields', []),
                    $this->getContentTypeConfigValue($contentTypeName, 'message_attachments_fields', []),
                    $this->getContentTypeConfigValue($contentTypeName, 'implode_glue', "\n"),
                    $this->getContentTypeConfigValue($contentTypeName, 'message_name'),
                    $this->getContentTypeConfigValue($contentTypeName, 'email_configuration_name'),
                    $this->getContentTypeConfigValue($contentTypeName, 'sender_name'),
                    $this->getContentTypeConfigValue($contentTypeName, 'receiver_name')
                );
                $config->addContentType($contentTypeName, $contentType);
            }
        }
    }

    private function registerRequestDataTransformer(Application $app)
    {
        $app[RequestDataTransformer::class] = $app->share(
            function ($app) {
                return new RequestDataTransformer(
                    $app['validator'],
                    $app[Config::class],
                    $app[ContentConstraintsFactory::class]
                );
            }
        );
    }

    private function registerContentTypeValidatorConstraintsFactory(Application $app)
    {
        $app[ContentConstraintsFactory::class] = $app->share(
            function ($app) {
                return new ContentConstraintsFactory($app[Translator::class]);
            }
        );
    }

    private function registerTranslator(Application $app)
    {
        $app[Translator::class] = $app->share(
            function ($app) {
                return new Translator();
            }
        );
    }

    private function registerContentRepository(Application $app)
    {
        $app[ContentRepository::class] = $app->share(
            function ($app) {
                return new ContentRepository($app['storage']);
            }
        );
    }

    private function registerUploader(Application $app)
    {
        $app[Uploader::class] = $app->share(
            function ($app) {
                return new Uploader($app['filesystem'], $app[Config::class]);
            }
        );
    }

    private function registerAttachMediaToContentAction(Application $app)
    {
        $app[AttachMediaToContentAction::class] = $app->share(
            function ($app) {
                return new AttachMediaToContentAction(
                    $app[ContentRepository::class],
                    $app[Uploader::class],
                    $app[Mailer::class]
                );
            }
        );
    }

    private function registerCorsListener(Application $app)
    {
        $app[CorsListener::class] = $app->share(
            function ($app) {
                /** @var Config $config */
                $config = $app[Config::class];
                $paths = [
                    '^'.$config->getApiPrefix().'/' => []
                ];

                return new CorsListener(
                    $app['dispatcher'],
                    $paths,
                    $this->config['cors']
                );
            }
        );
    }
}
