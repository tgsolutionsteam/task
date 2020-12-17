<?php

namespace App\Factories;

use Exception;
use ReflectionClass;
use Phalcon\Http\Response;

class ErrorFactory implements ErrorInterface
{
    protected $response;
    protected $exception;
    protected $exceptionFactory;

    public function __construct(Response $response, Exception $exception)
    {
        $this->response = $response;
        $this->exception = $exception;
        $this->buildExceptionFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function buildErrorMessage(): void
    {
        $this->response->setStatusCode(409, "Conflict");
        $this->response->setContentType('application/json');
        $this->response->setJsonContent([
            'message' => $this->exceptionFactory->getErrorMessage()
        ]);
        $this->response->setHeader('Content-Length', strlen($this->response->getContent()));
    }

    private function buildExceptionFactory()
    {
        $reflectionClass = new ReflectionClass($this->exception);
        $classnameError = 'App\\Errors\\' . $reflectionClass->getShortName() . 'Error';
        if (!class_exists($classnameError)) {
            $classnameError = 'App\\Errors\\DefaultExceptionError';
        }
        $this->exceptionFactory = new $classnameError($this->exception);
    }
}
