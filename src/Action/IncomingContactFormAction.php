<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Action;

use Bolt\Extension\Kryst3q\RestApiContactForm\Mailer\Mailer;
use Bolt\Extension\Kryst3q\RestApiContactForm\Mailer\Message;
use Bolt\Extension\Kryst3q\RestApiContactForm\Storage\Entity\ContactForm;
use Bolt\Extension\Kryst3q\RestApiContactForm\Storage\Repository\ContactFormRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class IncomingContactFormAction
{
    /**
     * @var ContactFormRepository
     */
    private $repository;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(ContactFormRepository $repository, Mailer $mailer)
    {
        $this->repository = $repository;
        $this->mailer = $mailer;
    }

    /**
     * @param ContactForm $contactForm
     * @return JsonResponse
     */
    public function handle(ContactForm $contactForm)
    {
        $success = $this->saveContactForm($contactForm);

//        if (!$success) {
//            /*
//             * TODO: handle case when saving to db was unsuccesful
//             */
//        }

        $success = $this->sendEmailWithContactMessage($contactForm);

//        if (!$success) {
//            /*
//             * TODO: handle case when email was not sent
//             */
//        }

        return new JsonResponse(['message' => 'Dziękujemy za kontakt!'], Response::HTTP_OK);
    }

    /**
     * @param ContactForm $contactForm
     * @return bool
     */
    private function saveContactForm(ContactForm $contactForm)
    {
        return $this->repository->save($contactForm);
    }

    /**
     * @param ContactForm $contactForm
     * @return int
     */
    private function sendEmailWithContactMessage(ContactForm $contactForm)
    {
        $messageContent = sprintf(
            "%s (%s) napisał:\n\"%s\"",
            $contactForm->getName(),
            $contactForm->getPhoneNumber(),
            $contactForm->getMessage()
        );
        $message = new Message($messageContent);

        return $this->mailer->send($message);
    }
}
