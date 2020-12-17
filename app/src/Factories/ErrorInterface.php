<?php

namespace App\Factories;

/**
 * App\Factories\ErrorInterface
 *
 * This interface ensures that error classes are able to render messages and respond with an user-friendly message
 */
interface ErrorInterface
{
    /**
     * Method to build a response of the type Phalcon\Http\Response
     * with the properly user friendly message based on the type of Exception
     *
     * @return void
     */
    public function buildErrorMessage(): void;
}
