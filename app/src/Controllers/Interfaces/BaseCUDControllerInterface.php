<?php

namespace App\Controllers\Interfaces;

interface BaseCUDControllerInterface
{

    /**
     * This action is used to create a element to a resource
     * You need to pass a valid body
     * See OAS to more details
     * @return array
     */
    public function create(): array;

    /**
     * This action is used to create a element to a resource
     * You need to pass a valid body and an Id in URI
     * See OAS to more details
     * @param int $id
     * @return array
     */
    public function update(int $id): array;
}
