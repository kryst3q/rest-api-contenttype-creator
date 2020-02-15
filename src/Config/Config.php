<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Config;

class Config
{
    /**
     * @var string
     */
    private $apiPrefix;

    /**
     * @var EmailConfig
     */
    private $emailConfig;

    /**
     * @var SenderConfig
     */
    private $senderConfig;

    /**
     * @var ReceiverConfig
     */
    private $receiverConfig;

    /**
     * @var MessageConfig
     */
    private $messageConfig;

    public function __construct(
        $apiPrefix,
        EmailConfig $emailConfig,
        SenderConfig $senderConfig,
        ReceiverConfig $receiverConfig,
        MessageConfig $messageConfig
    ) {
        $this->apiPrefix = $apiPrefix;
        $this->emailConfig = $emailConfig;
        $this->senderConfig = $senderConfig;
        $this->receiverConfig = $receiverConfig;
        $this->messageConfig = $messageConfig;
    }

    /**
     * @return string
     */
    public function getApiPrefix()
    {
        return $this->apiPrefix;
    }

    /**
     * @return EmailConfig
     */
    public function getEmailConfig()
    {
        return $this->emailConfig;
    }

    /**
     * @return SenderConfig
     */
    public function getSenderConfig()
    {
        return $this->senderConfig;
    }

    /**
     * @return ReceiverConfig
     */
    public function getReceiverConfig()
    {
        return $this->receiverConfig;
    }

    /**
     * @return MessageConfig
     */
    public function getMessageConfig()
    {
        return $this->messageConfig;
    }
}
