<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Action;

use Bolt\Exception\InvalidRepositoryException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ContentType;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\MessageConfig;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\UnsuccessfulContentSaveException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Mailer\Mailer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Mailer\Message;
use Bolt\Storage\Entity\Content;
use Bolt\Storage\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class IncomingContentTypeFormAction
{
    /**
     * @var EntityManager
     */
    private $storage;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var Config
     */
    private $config;

    public function __construct(EntityManager $storage, Mailer $mailer, Config $config)
    {
        $this->storage = $storage;
        $this->mailer = $mailer;
        $this->config = $config;
    }

    /**
     * @param Content $content
     * @return JsonResponse
     * @throws UnsuccessfulContentSaveException
     * @throws InvalidRepositoryException
     */
    public function handle(Content $content)
    {
        $repository = $this->storage->getRepository($content->getContenttype());
        $success = $repository->save($content);

        if (!$success) {
            throw new UnsuccessfulContentSaveException();
        }

        $contentType = $this->config->getContentType($content->getContenttype());

        if ($contentType->hasToSendEmail()) {
            $messageChunks = $this->getMessageChunks($content, $contentType);
            $message = $this->prepareMessage($messageChunks, $contentType);

            /*
             * TODO: add queueing messages and send them by cron task
             */
            $success = $this->sendEmailWithContactMessage($message);

            if ($success) {
                $content->setStatus('published');
            }

            /*
             * TODO: handle case when email was not sent
             */
//        if (!$success) {
//
//        }
        }

        /*
         * TODO: add translations
         */
        return new JsonResponse(['message' => 'Dziękujemy za kontakt!'], Response::HTTP_OK);
    }

    /**
     * @param Message $message
     * @return int
     */
    private function sendEmailWithContactMessage(Message $message)
    {
        return $this->mailer->send($message);
    }

    /**
     * @param string[] $messageChunks
     * @param ContentType $contentType
     * @return Message
     */
    private function prepareMessage(array $messageChunks, ContentType $contentType)
    {
        $message = Message::fromChunks($messageChunks, $contentType->getImplodeGlue());

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
}
