<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Mailer;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Swift_Mailer;
use Swift_Message;
use Swift_Transport;

class Mailer
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config,
        Swift_Transport $transport
    ) {
        $this->mailer = new Swift_Mailer($transport);
        $this->config = $config;
    }

    /**
     * @param Message $message
     * @return int
     */
    public function send(Message $message)
    {
        return $this->mailer->send($this->prepareSwiftMailerMessage($message));
    }

    /**
     * @param Message $message
     * @return Swift_Message
     */
    private function prepareSwiftMailerMessage(Message $message)
    {
        $messageConfig = $this->config->getMessageConfig(Config::DEFAULT_CONFIG_NAME);
        $subject = $messageConfig->getSubject();

        if (null !== $message->getSubject()) {
            $subject = $message->getSubject();
        }

        $senderConfig = $this->config->getSenderConfig(Config::DEFAULT_CONFIG_NAME);

        if (null !== $message->getSenderConfig()) {
            $senderConfig = $message->getSenderConfig();
        }

        $receiverConfig = $this->config->getReceiverConfig(Config::DEFAULT_CONFIG_NAME);

        if (null !== $message->getReceiverConfig()) {
            $receiverConfig = $message->getReceiverConfig();
        }

        $swiftMessage = new Swift_Message(
            $subject,
            $message->getContent()
        );
        $swiftMessage->setFrom(
            $senderConfig->getEmail(),
            $senderConfig->getName()
        );
        $swiftMessage->setTo(
            $receiverConfig->getEmail(),
            $receiverConfig->getName()
        );

        return $swiftMessage;
    }
}
