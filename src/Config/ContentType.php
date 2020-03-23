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
     * @var string
     */
    private $sendEmailAction;

    /**
     * @var string
     */
    private $contentTypeName;

    /**
     * @var string[]
     */
    private $messageAttachmentsNames;

    /**
     * @param string $contentTypeName
     * @param array $fields
     * @param bool $sendEmail
     * @param string $sendEmailAction
     * @param string[] $messageFieldsNames
     * @param string[] $messageAttachmentsNames
     * @param string|null $implodeGlue
     * @param string|null $messageConfigName
     * @param string|null $emailConfigurationName
     * @param string|null $senderConfigName
     * @param string|null $receiverConfigName
     */
    public function __construct(
        $contentTypeName,
        array $fields,
        $sendEmail = false,
        $sendEmailAction,
        array $messageFieldsNames = [],
        array $messageAttachmentsNames = [],
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
        $this->sendEmailAction = $sendEmailAction;
        $this->contentTypeName = $contentTypeName;
        $this->messageAttachmentsNames = $messageAttachmentsNames;
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

    /**
     * @param string $fieldName
     * @return bool
     */
    public function hasField($fieldName)
    {
        return isset($this->fields[$fieldName]);
    }

    /**
     * @param string $fieldName
     * @return array
     */
    public function getField($fieldName)
    {
        return $this->fields[$fieldName];
    }

    /**
     * @return string
     */
    public function getSendEmailAction()
    {
        return $this->sendEmailAction;
    }

    /**
     * @return string
     */
    public function getContentTypeName()
    {
        return $this->contentTypeName;
    }

    /**
     * @return string[]
     */
    public function getMessageAttachmentsNames()
    {
        return $this->messageAttachmentsNames;
    }
}
