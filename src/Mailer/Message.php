<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Mailer;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ReceiverConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\SenderConfig;
use Swift_Attachment;

class Message
{
    private static $implodeGlue = "\n";

    /**
     * @var string
     */
    private $content;

    /**
     * @var string|null
     */
    private $subject;

    /**
     * @var SenderConfig|null
     */
    private $senderConfig;

    /**
     * @var ReceiverConfig|null
     */
    private $receiverConfig;

    /**
     * @var Swift_Attachment[]
     */
    private $attachments = [];

    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * @param string[] $chunks
     * @param string|null $implodeGlue
     * @return Message
     */
    public static function fromChunks(array $chunks, $implodeGlue)
    {
        if (null === $implodeGlue) {
            $implodeGlue = self::$implodeGlue;
        }

        return new self(implode($implodeGlue, $chunks));
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return SenderConfig|null
     */
    public function getSenderConfig()
    {
        return $this->senderConfig;
    }

    /**
     * @return ReceiverConfig|null
     */
    public function getReceiverConfig()
    {
        return $this->receiverConfig;
    }

    /**
     * @return Swift_Attachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param string|null $subject
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param SenderConfig|null $senderConfig
     * @return Message
     */
    public function setSenderConfig($senderConfig)
    {
        $this->senderConfig = $senderConfig;
        return $this;
    }

    /**
     * @param ReceiverConfig|null $receiverConfig
     * @return Message
     */
    public function setReceiverConfig($receiverConfig)
    {
        $this->receiverConfig = $receiverConfig;
        return $this;
    }

    /**
     * @param Swift_Attachment[] $attachments
     * @return $this
     */
    public function setAttachments(array $attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }
}
