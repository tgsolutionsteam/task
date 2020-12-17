<?php

namespace App\Errors\Messages;

/**
 * App\Errors\ErrorsMessageInterface
 *
 * phpcs:ignore
 * Each error class must have a method for returning an array of responses. This interface was created to guarantee this.
 */
interface ErrorsMessageInterface
{
    /**
     * Return an array of user friendly messages with the respectively key values as error code
     *
     * @return array
     */
    public function getMessages(): array;
}
