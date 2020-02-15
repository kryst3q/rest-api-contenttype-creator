<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Config;

class MessageConfig
{
    /**
     * @var string
     */
    private $subject;

    /**
     * Path to email template.
     *
     * @var string
     */
    private $template;

    public function __construct($subject, $template)
    {
        $this->subject = $subject;
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
