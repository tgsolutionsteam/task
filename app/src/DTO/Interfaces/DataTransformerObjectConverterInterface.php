<?php

namespace App\DTO\Interfaces;

use Phalcon\Http\RequestInterface;

/**
 * This interface ensures that the Transformer Service has the necessary transform method for the intended response
 */
interface DataTransformerObjectConverterInterface
{
    /**
     * This method is a handle to pass the Request to correct DTO
     * @param RequestInterface $request
     * @return DTOInterface
     */
    public function apply(RequestInterface $request): DTOInterface;
}
