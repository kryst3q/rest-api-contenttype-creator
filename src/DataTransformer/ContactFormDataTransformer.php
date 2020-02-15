<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\DataTransformer;

use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidArgumentException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Storage\Entity\ContactForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactFormDataTransformer
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param null $dataTransformer
     * @param Request $request
     * @return ContactForm
     * @throws \Exception
     */
    public function transform($dataTransformer, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $contactForm = new ContactForm();
        $contactForm->setName(isset($data['name']) ? $data['name'] : '');
        $contactForm->setPhoneNumber(isset($data['email']) ? $data['email'] : '');
        $contactForm->setMessage(isset($data['message']) ? $data['message'] : '');

        $violations = $this->validator->validate($contactForm);

        if ($violations->count() > 0) {
            throw new InvalidArgumentException($violations);
        }

        return $contactForm;
    }
}
