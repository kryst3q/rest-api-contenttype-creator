<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Mailer;

class Message
{
    /**
     * @var array
     */
    private $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }
}
