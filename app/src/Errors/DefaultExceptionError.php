<?php

namespace App\Errors;

use Exception;

class DefaultExceptionError implements ExceptionInterface
{
    private $exception;
    private $message;

    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
        $this->message = $exception->getMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage(): string
    {
        return $this->message;
    }
}
