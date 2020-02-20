<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Config;

class ContentType
{
    /**
     * copy of "fields" attribute from given contenttype from contenttypes.yml
     * @var array
     */
    private $fields;

    /**
     * @var bool
     */
    private $sendEmail;

    /**
     * @var string[]
     */
    private $messageFieldsNames = [];

    /**
     * @var string|null
     */
    private $implodeGlue;

    /**
     * @var string|null
     */
    private $messageConfigName;

    /**
     * @var string|null
     */
    private $emailConfigurationName;

    /**
     * @var string|null
     */
    private $senderConfigName;

    /**
     * @var string|null
     */
    private $receiverConfigName;

    /**
     * @param array $fields
     * @param bool $sendEmail
     * @param string[] $messageFieldsNames
     * @param string|null $implodeGlue
     * @param string|null $messageConfigName
     * @param string|null $emailConfigurationName
     * @param string|null $senderConfigName
     * @param string|null $receiverConfigName
     */
    public function __construct(
        array $fields,
        $sendEmail = false,
        array $messageFieldsNames = [],
        $implodeGlue = null,
        $messageConfigName = null,
        $emailConfigurationName = null,
        $senderConfigName = null,
        $receiverConfigName = null
    ) {
        $this->fields = $fields;
        $this->sendEmail = $sendEmail;
        $this->messageFieldsNames = $messageFieldsNames;
        $this->implodeGlue = $implodeGlue;
        $this->messageConfigName = $messageConfigName;
        $this->emailConfigurationName = $emailConfigurationName;
        $this->senderConfigName = $senderConfigName;
        $this->receiverConfigName = $receiverConfigName;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return string[]
     */
    public function getFieldsNames()
    {
        return array_keys($this->getFields());
    }

    /**
     * @return bool
     */
    public function hasToSendEmail()
    {
        return $this->sendEmail;
    }

    /**
     * @return string[]
     */
    public function getMessageFieldsNames()
    {
        return $this->messageFieldsNames;
    }

    /**
     * @return string|null
     */
    public function getImplodeGlue()
    {
        return $this->implodeGlue;
    }

    /**
     * @return string|null
     */
    public function getMessageConfigName()
    {
        return $this->messageConfigName;
    }

    /**
     * @return string|null
     */
    public function getEmailConfigurationName()
    {
        return $this->emailConfigurationName;
    }

    /**
     * @return string|null
     */
    public function getSenderConfigName()
    {
        return $this->senderConfigName;
    }

    /**
     * @return string|null
     */
    public function getReceiverConfigName()
    {
        return $this->receiverConfigName;
    }
}