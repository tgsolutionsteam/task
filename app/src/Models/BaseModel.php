<?php

namespace App\Models;

use App\Models\Interfaces\DAOInterface;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\ModelInterface;

abstract class BaseModel extends Model implements DAOInterface, ModelInterface
{
    /**
     * {@inheritdoc}
     */
    public function getColumns(array $fields = null)
    {
        if (empty($fields)) {
            $fields = $this->getModelsMetaData()->getAttributes($this);
        }

        return implode(",", $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        $keys = $this->getModelsMetaData()->getPrimaryKeyAttributes($this);
        return $keys[0] ?? null;
    }
}
