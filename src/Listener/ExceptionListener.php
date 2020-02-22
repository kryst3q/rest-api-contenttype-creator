<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Listener;

use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidArgumentException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\TranslatedException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Translator\Translator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExceptionListener
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Exception $exception
     * @return JsonResponse
     */
    public function handle(\Exception $exception)
    {
        if ($exception instanceof InvalidArgumentException) {
            $data = $this->handleInvalidArgumentException($exception);
            $statusCode = Response::HTTP_BAD_REQUEST;
        } elseif ($exception instanceof TranslatedException) {
            $data = $this->translator->trans($exception->getMessage(), $exception->getParameters());
            $statusCode = Response::HTTP_BAD_REQUEST;
        } else {
            $data = $exception->getMessage();
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new JsonResponse($data, $statusCode);
    }

    /**
     * @param InvalidArgumentException $exception
     * @return array
     */
    private function handleInvalidArgumentException(InvalidArgumentException $exception)
    {
        $data = ['errors' => []];

        foreach ($exception->getViolations() as $error) {
            $data['errors'][$error->getPropertyPath()] = $error->getMessage();
        }

        return $data;
    }
}
