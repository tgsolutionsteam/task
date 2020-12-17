<?php

namespace App\Errors;

/**
 * App\Errors\ExceptionInterface
 *
 * Each error class must handle the response according to its needs. This interface was created to guarantee this.
 */
interface ExceptionInterface
{
    /**
     * Get the user friendly error message based on type of exception
     *
     * @return string
     */
    public function getErrorMessage(): string;
}
