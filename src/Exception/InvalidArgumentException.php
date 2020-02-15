<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidArgumentException extends \Exception
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        parent::__construct('');

        $this->violations = $violations;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }
}