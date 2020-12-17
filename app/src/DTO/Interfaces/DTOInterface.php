<?php

namespace App\DTO\Interfaces;

use Phalcon\Mvc\ModelInterface;

interface DTOInterface
{
    /**
     * This method is used to convert a request in a DTO to reuse in repositories and controllers
     * @param array $queryString
     * @param object $body
     * @return DTOInterface
     */
    public function convert(array $queryString, object $body): DTOInterface;

    /**
     * This method is to  transform a model in a DTO
     * @param  ModelInterface $model
     * @return DTOInterface
     */
    public function transform(ModelInterface $model): DTOInterface;
}
