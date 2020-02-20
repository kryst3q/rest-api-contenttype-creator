<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Config;

class Config
{
    const DEFAULT_CONFIG_NAME = 'default';

    /**
     * @var string
     */
    private $apiPrefix;

    /**
     * @var EmailConfig[]
     */
    private $emailConfigs;

    /**
     * @var SenderConfig[]
     */
    private $senderConfigs;

    /**
     * @var ReceiverConfig[]
     */
    private $receiverConfigs;

    /**
     * @var MessageConfig[]
     */
    private $messageConfigs;

    /**
     * @var array
     */
    private $contentTypes;

    public function __construct($apiPrefix)
    {
        $this->apiPrefix = $apiPrefix;
    }

    /**
     * @param string $emailConfigName
     * @param EmailConfig $emailConfig
     */
    public function addEmailConfig($emailConfigName, EmailConfig $emailConfig)
    {
        $this->emailConfigs[$emailConfigName] = $emailConfig;
    }

    /**
     * @param string $name
     * @param SenderConfig $senderConfig
     */
    public function addSenderConfig($name, SenderConfig $senderConfig)
    {
        $this->senderConfigs[$name] = $senderConfig;
    }

    /**
     * @param string $name
     * @param ReceiverConfig $receiverConfig
     */
    public function addReceiverConfig($name, ReceiverConfig $receiverConfig)
    {
        $this->receiverConfigs[$name] = $receiverConfig;
    }

    /**
     * @param string $name
     * @param MessageConfig $messageConfig
     */
    public function addMessageConfig($name, MessageConfig $messageConfig)
    {
        $this->messageConfigs[$name] = $messageConfig;
    }

    /**
     * @param string $contentTypeName
     * @param ContentType $contentType
     */
    public function addContentType($contentTypeName, ContentType $contentType)
    {
        $this->contentTypes[$contentTypeName] = $contentType;
    }

    /**
     * @return string
     */
    public function getApiPrefix()
    {
        $apiPrefix = trim($this->apiPrefix);

        if (strpos($apiPrefix, '/') !== 0) {
            $apiPrefix = '/'.$apiPrefix;
        }

        return $apiPrefix;
    }

    /**
     * @param string $contentTypeName
     * @return bool
     */
    public function hasContentType($contentTypeName)
    {
        return isset($this->contentTypes[$contentTypeName]);
    }

    /**
     * @return string[]
     */
    public function getContentTypesNames()
    {
        return array_keys($this->contentTypes);
    }

    /**
     * @param string $contentTypeName
     * @return ContentType
     */
    public function getContentType($contentTypeName)
    {
        return $this->contentTypes[$contentTypeName];
    }

    /**
     * @param string|null $messageConfigName
     * @return MessageConfig|null
     */
    public function getMessageConfig($messageConfigName)
    {
        if (null === $messageConfigName) {
            return null;
        }

        return $this->messageConfigs[$messageConfigName];
    }

    /**
     * @param string|null $receiverConfigName
     * @return ReceiverConfig|null
     */
    public function getReceiverConfig($receiverConfigName)
    {
        if (null === $receiverConfigName) {
            return null;
        }

        return $this->receiverConfigs[$receiverConfigName];
    }

    /**
     * @param string|null $senderConfigName
     * @return SenderConfig|null
     */
    public function getSenderConfig($senderConfigName)
    {
        if (null === $senderConfigName) {
            return null;
        }

        return $this->senderConfigs[$senderConfigName];
    }

    /**
     * @param string|null $emailConfigName
     * @return EmailConfig|null
     */
    public function getEmailConfig($emailConfigName)
    {
        if (null === $emailConfigName) {
            return null;
        }

        return $this->emailConfigs[$emailConfigName];
    }
}
