<?php

namespace App\DTO\Interfaces;

interface BaseDTOInterface
{
    /**
     * This method is used to convert a request in a DTO to reuse in repositories and controllers
     * @param array $queryString
     * @param object $body
     * @return DTOInterface
     */
    public function convert(array $queryString, object $body): DTOInterface;
}
