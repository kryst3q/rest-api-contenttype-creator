<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Factory;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ContentType;
use Bolt\Extension\Kryst3q\RestApiContactForm\Translator\Translator;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ContentTypeValidatorConstraintsFactory
{
    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_INTEGER = 'integer';
    const FIELD_TYPE_FLOAT = 'float';
    const FIELD_TYPE_TEXTAREA = 'textarea';
    const FIELD_TYPE_MARKDOWN = 'markdown';
    const FIELD_TYPE_DATETIME = 'datetime';
    const FIELD_TYPE_DATE = 'date';
    const FIELD_TYPE_SELECT = 'select';
    const FIELD_TYPE_CHECKBOX = 'checkbox';

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param ContentType $contentType
     * @return Collection
     */
    public function getValidatorConstraints(ContentType $contentType)
    {
        $constraints = [];

        foreach ($contentType->getFields() as $fieldName => $fieldMetadata) {
            $fieldConstraints = $this->getConstraints($fieldMetadata['type']);

            if ($this->fieldIsRequired($fieldMetadata)) {
                $this->addRequireConstraint($fieldConstraints);
            }

            $this->configureConstraints($fieldName, $fieldMetadata, $fieldConstraints);

            $constraints[$fieldName] = $fieldConstraints;
        }

        return new Collection(['fields' => $constraints, 'allowExtraFields' => ['contentType']]);
    }

    /**
     * @param string $fieldType
     * @return array
     */
    private function getConstraints($fieldType)
    {
        switch ($fieldType) {
            case self::FIELD_TYPE_TEXT:
                return [
                    new Type([
                        'type' => 'string',
                        'message' => $this->translateTypeConstraintMessage('string')
                    ]),
                    new Length([
                        'max' => 256,
                        'maxMessage' => $this->translateMaxLengthConstraintMessage(256)
                    ])
                ];
            case self::FIELD_TYPE_INTEGER:
                return [
                    new Type([
                        'type' => 'int',
                        'message' => $this->translateTypeConstraintMessage('int')
                    ])
                ];
            case self::FIELD_TYPE_FLOAT:
                return [
                    new Type([
                        'type' => 'float',
                        'message' => $this->translateTypeConstraintMessage('float')
                    ])
                ];
            case self::FIELD_TYPE_TEXTAREA:
            case self::FIELD_TYPE_MARKDOWN:
                return [
                    new Type([
                        'type' => 'string',
                        'message' => $this->translateTypeConstraintMessage('string')
                    ]),
                    new Length([
                        'max' => 32768,
                        'maxMessage' => $this->translateMaxLengthConstraintMessage(32768)
                    ])
                ];
            case self::FIELD_TYPE_DATETIME:
                return [
                    new DateTime()
                ];
            case self::FIELD_TYPE_DATE:
                return [
                    new Date()
                ];
            case self::FIELD_TYPE_SELECT:
                return [
                    new Choice()
                ];
            case self::FIELD_TYPE_CHECKBOX:
                return [
                    new Type([
                        'type' => 'bool',
                        'message' => $this->translateTypeConstraintMessage('boolean')
                    ])
                ];
            default:
                return [];
        }
    }

    /**
     * @param $fieldMetadata
     * @return bool
     */
    private function fieldIsRequired($fieldMetadata)
    {
        return isset($fieldMetadata['required']) && true === $fieldMetadata['required'];
    }

    /**
     * @param array $fieldConstraints
     */
    private function addRequireConstraint(array &$fieldConstraints)
    {
        $requireConstraint = [
            new NotBlank([
                'message' => $this->translator->trans('This field is required.')
            ])
        ];
        $fieldConstraints = array_merge($requireConstraint, $fieldConstraints);
    }

    /**
     * @param array $fieldMetadata
     * @param array $fieldConstraints
     */
    private function configureSelectTypeFieldConstraints(array $fieldMetadata, array &$fieldConstraints)
    {
        if (is_array($fieldMetadata['values'])) {
            $fieldConstraints[0]->choices = $fieldMetadata['values'];
        }

        if (isset($fieldMetadata['multiple'])) {
            $fieldConstraints[0]->multiple = $fieldMetadata['multiple'];
        }
    }

    /**
     * @param string $fieldName
     * @param array $fieldConstraints
     */
    private function configureTextTypeFieldConstraints($fieldName, array &$fieldConstraints)
    {
        if (strpos($fieldName, 'email') !== false) {
            $fieldConstraints[] = new Email([
                'message' => $this->translator->trans('This value is not a valid email address.')
            ]);
        }
    }

    /**
     * @param string $fieldName
     * @param array $fieldMetadata
     * @param array $fieldConstraints
     */
    private function configureConstraints($fieldName, $fieldMetadata, &$fieldConstraints)
    {
        switch ($fieldMetadata['type']) {
            case self::FIELD_TYPE_TEXT:
                $this->configureTextTypeFieldConstraints($fieldName, $fieldConstraints);
                break;
            case self::FIELD_TYPE_SELECT:
                $this->configureSelectTypeFieldConstraints($fieldMetadata, $fieldConstraints);
                break;
        }
    }

    /**
     * @param string $type
     * @return string
     */
    private function translateTypeConstraintMessage($type)
    {
        return $this->translator->trans(
            'This value should be of type {type}.',
            [
                '{type}' => $type
            ]
        );
    }

    /**
     * @param int $limit
     * @return string
     */
    private function translateMaxLengthConstraintMessage($limit)
    {
        return $this->translator->trans(
            'This value is too long. It should have {limit} character or less.',
            [
                '{limit}' => $limit
            ]
        );
    }
}
