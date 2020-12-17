<?php

namespace App\Controllers\Interfaces;

interface BaseQueryControllerInterface
{
    /**
     * This action is used to list all elements from a resource
     * You can use any field to filter or order results
     * See OAS to more details
     * @return array
     */
    public function list(): array;

    /**
     * This action is used to list a especific element from a resource
     * See OAS to more details
     * @param int $id
     * @return array
     */
    public function show(int $id): array;

    /**
     * This method is used to get all params passed via query string and popuplate
     * the variables from context
     * @return array
     */
    public function getParams(): array;

    /**
     * This method is used to set global options from query string to context
     * @return void
     */
    public function setOptions(): void;
}
