<?php

namespace App\Controllers\Interfaces;

interface ControllerInterface
{
    /**
     * The system uses dependency injection, and to work correctly you need to inject
     * the repository and DTO.
     * Example:
     *      public function onConstruct() {
     *          $this->repository = new AnyRepository(new AnyModel());
     *          $this->dto = (new DataTransformerObjectConverter(new StudentsDTO()))->apply($this->request);
     *      }
     *
     */
    public function onConstruct();
}
