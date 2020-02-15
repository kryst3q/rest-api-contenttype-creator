<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Storage\Entity;

use Bolt\Storage\Entity\Content;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class ContactForm extends Content
{
    const TABLE_NAME = 'contact_forms';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $message;

//    private $attachments;

    public function __construct($data = [])
    {
        parent::__construct($data);

        $this->status = 'draft';
    }

    /**
     * @param ClassMetadata $metadata
     */
    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints('name', [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string'])
        ]);
        $metadata->addPropertyConstraints('phoneNumber', [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Length(['min' => 12, 'max' => 12])
        ]);
        $metadata->addPropertyConstraints('message', [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Length(['max' => 144])
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ContactForm
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return ContactForm
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ContactForm
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}
