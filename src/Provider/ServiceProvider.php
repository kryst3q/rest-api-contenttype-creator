<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Provider;

use Bolt\Extension\Kryst3q\RestApiContactForm\Action\IncomingContentTypeFormAction;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ContentType;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\EmailConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\MessageConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ReceiverConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\SenderConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\DataTransformer\RequestDataTransformer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Factory\ContentTypeValidatorConstraintsFactory;
use Bolt\Extension\Kryst3q\RestApiContactForm\Listener\ExceptionListener;
use Bolt\Extension\Kryst3q\RestApiContactForm\Mailer\Mailer;
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
        $this->registerMailer($app);
        $this->registerContentTypeValidatorConstraintsFactory($app);
        $this->registerRequestDataTransformer($app);
        $this->registerIncomingContentTypeFormAction($app);
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
    private function registerIncomingContentTypeFormAction(Application $app)
    {
        $app[IncomingContentTypeFormAction::class] = $app->share(
            function ($app) {
                return new IncomingContentTypeFormAction(
                    $app['storage'],
                    $app[Mailer::class],
                    $app[Config::class]
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
        $emailConfig = $app[Config::class]->getEmailConfig(Config::DEFAULT_CONFIG_NAME);
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

    /**
     * @param Config $config
     */
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

    /**
     * @param Config $config
     */
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

    /**
     * @param Config $config
     */
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

    /**
     * @param Config $config
     */
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

    /**
     * @param Application $app
     * @param Config $config
     */
    private function prepareContentTypes(Application $app, Config $config)
    {
        $availableContentTypes = $app['config']->get('contenttypes');
        $availableContentTypesNames = array_keys($availableContentTypes);

        foreach (array_keys($this->config['content_type']) as $contentTypeName) {
            if (in_array($contentTypeName, $availableContentTypesNames)) {
                $contentType = new ContentType(
                    $availableContentTypes[$contentTypeName]['fields'],
                    $this->getContentTypeConfigValue($contentTypeName, 'send_email', null) === true,
                    $this->getContentTypeConfigValue($contentTypeName, 'message_fields', []),
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

    /**
     * @param Application $app
     */
    private function registerRequestDataTransformer(Application $app)
    {
        $app[RequestDataTransformer::class] = $app->share(
            function ($app) {
                return new RequestDataTransformer(
                    $app['validator'],
                    $app[Config::class],
                    $app[ContentTypeValidatorConstraintsFactory::class]
                );
            }
        );
    }

    /**
     * @param Application $app
     */
    private function registerContentTypeValidatorConstraintsFactory(Application $app)
    {
        $app[ContentTypeValidatorConstraintsFactory::class] = $app->share(
            function () {
                return new ContentTypeValidatorConstraintsFactory();
            }
        );
    }
}
