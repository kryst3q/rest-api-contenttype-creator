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
        $swiftMessage = new Swift_Message(
            $this->config->getMessageConfig()->getSubject(),
            $message->getContent()
        );
        $swiftMessage->setFrom([
            $this->config->getSenderConfig()->getEmail() =>$this->config->getSenderConfig()->getName()
        ]);
        $swiftMessage->setTo([
            $this->config->getReceiverConfig()->getEmail() =>$this->config->getReceiverConfig()->getName()
        ]);

        return $swiftMessage;
    }
}
