<?php

namespace App\Repositories\Interfaces;

use Phalcon\Mvc\Model\Row;

interface BaseRepositoryInterface
{
    /**
     * This methods are explicite are used to manipulates the model
     */
    public function setLimit(int $limit): void;

    /**
     * This methods are explicite are used to manipulates the model
     */
    public function setOffset(int $offset): void;

    /**
     * This methods are explicite are used to manipulates the model
     */
    public function findFirst(int $id): Row;

    /**
     * This methods are explicite are used to manipulates the model
     */
    public function find(array $query, array $params = null): array;
}
