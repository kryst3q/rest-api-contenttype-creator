<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Mailer;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ContentType;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\MessageConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Factory\ContentConstraintsFactory;
use Bolt\Filesystem\FilesystemInterface;
use Bolt\Filesystem\Manager;
use Bolt\Storage\Entity\Content;
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

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(
        Config $config,
        Swift_Transport $transport,
        Manager $filesystemManager
    ) {
        $this->mailer = new Swift_Mailer($transport);
        $this->config = $config;
        $this->filesystem = $filesystemManager->getFilesystem('files');
    }

    /**
     * @param Content $content
     * @param string $action
     */
    public function sendEmail(Content $content, string $action)
    {
        $contentType = $this->config->getContentType((string) $content->getContenttype());

        if ($contentType->hasToSendEmail() && $contentType->getSendEmailAction() === $action) {
            $messageChunks = $this->getMessageChunks($content, $contentType);
            $attachments = $this->getAttachments($content, $contentType);
            $message = $this->prepareMessage($messageChunks, $attachments, $contentType);

            $success = $this->send($message);

            if ($success) {
                $content->setStatus('published');
            }
        }
    }

    /**
     * @param Message $message
     * @return int
     */
    private function send(Message $message)
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

        foreach ($message->getAttachments() as $attachment) {
            $swiftMessage->attach($attachment);
        }

        return $swiftMessage;
    }

    /**
     * @param string[] $messageChunks
     * @param \Swift_Attachment[] $attachments
     * @param ContentType $contentType
     * @return Message
     */
    private function prepareMessage(array $messageChunks, array  $attachments, ContentType $contentType)
    {
        $message = Message::fromChunks($messageChunks, $contentType->getImplodeGlue());
        $message->setAttachments($attachments);

        $messageConfig = $this->config->getMessageConfig($contentType->getMessageConfigName());
        if ($messageConfig instanceof MessageConfig) {
            $message->setSubject($messageConfig->getSubject());
        }
        $message->setReceiverConfig($this->config->getReceiverConfig($contentType->getReceiverConfigName()));
        $message->setSenderConfig($this->config->getSenderConfig($contentType->getSenderConfigName()));

        return $message;
    }

    /**
     * @param Content $content
     * @param ContentType $contentType
     * @return array
     */
    private function getMessageChunks(Content $content, ContentType $contentType)
    {
        $messageChunks = [];

        foreach ($contentType->getFieldsNames() as $fieldName) {
            if (in_array($fieldName, $contentType->getMessageFieldsNames())) {
                $messageChunks[] = $content->get($fieldName);
            }
        }
        return $messageChunks;
    }

    /**
     * @param Content $content
     * @param ContentType $contentType
     * @return \Swift_Attachment[]
     */
    private function getAttachments(Content $content, ContentType $contentType)
    {
        $attachments = [];
        $validFileTypes = [
            ContentConstraintsFactory::FIELD_TYPE_FILE,
            ContentConstraintsFactory::FIELD_TYPE_FILE_LIST
        ];

        foreach ($contentType->getFields() as $fieldName => $fieldData) {
            if (
                in_array($fieldName, $contentType->getMessageAttachmentsNames())
                && in_array($fieldData['type'], $validFileTypes)
            ) {
                $fieldValue = $content->get($fieldName);

                if (is_array($fieldValue)) {
                    foreach ($fieldValue as $item) {
                        $attachments[] = $this->prepareSwiftAttachment($item['filename']);
                    }
                } else {
                    $attachments[] = $this->prepareSwiftAttachment($fieldValue);
                }
            }
        }

        return $attachments;
    }

    /**
     * @param string $filePath
     * @return Swift_Attachment
     */
    private function prepareSwiftAttachment($filePath)
    {
        return \Swift_Attachment::newInstance(
            $this->filesystem->read($filePath),
            substr(
                $filePath,
                strrpos($filePath, '/')
            ),
            substr(
                $filePath,
                strrpos($filePath, '.')
            )
        );
    }
}
