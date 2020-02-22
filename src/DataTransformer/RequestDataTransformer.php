<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\DataTransformer;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ContentType;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidArgumentException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidBodyContentException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\NotHandledContentTypeException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Factory\ContentTypeValidatorConstraintsFactory;
use Bolt\Storage\Entity\Content;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDataTransformer
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ContentTypeValidatorConstraintsFactory
     */
    private $constraintsFactory;

    public function __construct(
        ValidatorInterface $validator,
        Config $config,
        ContentTypeValidatorConstraintsFactory $constraintsFactory
    ) {
        $this->validator = $validator;
        $this->config = $config;
        $this->constraintsFactory = $constraintsFactory;
    }

    /**
     * @param null $transformer
     * @param Request $request
     * @return Content
     * @throws InvalidArgumentException
     * @throws NotHandledContentTypeException
     * @throws InvalidBodyContentException
     */
    public function transform($transformer, Request $request)
    {
        $contentTypeName = $request->get('contentType');

        $this->validateIfRequestedContentTypeCanBeHandled($contentTypeName);

        $contentType = $this->config->getContentType($contentTypeName);

        $data = $this->prepareData($request, $contentType);

        return $this->prepareContent($contentTypeName, $data);
    }

    /**
     * @param string $contentTypeName
     * @throws NotHandledContentTypeException
     */
    private function validateIfRequestedContentTypeCanBeHandled($contentTypeName)
    {
        if (!in_array($contentTypeName, $this->config->getContentTypesNames())) {
            throw new NotHandledContentTypeException($contentTypeName);
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws InvalidBodyContentException
     */
    private function getInputData(Request $request)
    {
        $inputData = json_decode($request->getContent(), true);

        if (false === $inputData || null === $inputData) {
            throw new InvalidBodyContentException('json');
        }

        return $inputData;
    }

    /**
     * @param array $inputData
     * @param ContentType $contentType
     * @return array
     */
    private function sanitizeInputData($inputData, ContentType $contentType)
    {
        return array_intersect_key($inputData, $contentType->getFields());
    }

    /**
     * @param ContentType $contentType
     * @param array $data
     * @throws InvalidArgumentException
     */
    private function validateData(ContentType $contentType, array $data)
    {
        $constraints = $this->constraintsFactory->getValidatorConstraints($contentType);
        $errors = $this->validator->validate($data, $constraints);

        if (0 !== $errors->count()) {
            throw new InvalidArgumentException($errors);
        }
    }

    /**
     * @param string $contentTypeName
     * @param array $data
     * @return Content
     */
    private function prepareContent($contentTypeName, array $data)
    {
        $content = new Content();
        $content->setContenttype($contentTypeName);
        $content->setStatus('held');

        foreach ($data as $field => $value) {
            $content->set($field, $value);
        }

        return $content;
    }

    /**
     * @param Request $request
     * @param ContentType $contentType
     * @return array
     * @throws InvalidArgumentException
     * @throws InvalidBodyContentException
     */
    private function prepareData(Request $request, ContentType $contentType)
    {
        $inputData = $this->getInputData($request);
        $data = $this->sanitizeInputData($inputData, $contentType);
        $this->validateData($contentType, $data);

        return $data;
    }
}
