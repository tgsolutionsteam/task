<?php

namespace App\Repositories\Repositories;

use App\DTO\Interfaces\DTOInterface;
use App\Repositories\BaseRepositories\BaseRepository;
use App\Repositories\Interfaces\CUDRepositoryInterface;
use Exception;

class CurrenciesRepository extends BaseRepository implements CUDRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(DTOInterface $dto): ?DTOInterface
    {
        $this->model->symbol = $dto->getSymbol();

        if (!$this->model->create()) {
            throw new Exception('Error: ' . implode($this->model->getMessages(), ' | '));
        }

        return $dto->transform($this->model);
    }

    /**
     * {@inheritdoc}
     */
    public function update(DTOInterface $dto): ?DTOInterface
    {
        $model = $this->model->findFirst('id = ' . $dto->getId());
        $model->symbol = $dto->getSymbol();

        if (!$model->update()) {
            throw new Exception('Error: ' . implode($this->model->getMessages(), ' | '));
        }

        return $dto->transform($model);
    }
}
