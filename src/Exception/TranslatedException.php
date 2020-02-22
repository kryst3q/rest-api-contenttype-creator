<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Exception;

interface TranslatedException
{
    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return array
     */
    public function getParameters();
}
