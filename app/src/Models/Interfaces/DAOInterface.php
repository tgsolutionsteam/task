<?php

namespace App\Models\Interfaces;

/**
 * This methods are explicite are used to manipulates the model
 */
interface DAOInterface
{
    /**
     * Get columns from Model
     * @param array|null $fields
     * @return mixed
     */
    public function getColumns(array $fields = null);

    /**
     * Get primary key from model
     * @return mixed
     */
    public function getPrimaryKey();
}
