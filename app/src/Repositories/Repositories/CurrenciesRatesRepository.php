<?php

namespace App\Repositories\Repositories;

use App\DTO\Interfaces\DTOInterface;
use App\Repositories\BaseRepositories\BaseRepository;
use App\Repositories\Interfaces\CUDRepositoryInterface;
use Exception;

class CurrenciesRatesRepository extends BaseRepository implements CUDRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(DTOInterface $dto): ?DTOInterface
    {
        $this->model->currency_base_id = $dto->getCurrencyBaseId();
        $this->model->currency_id = $dto->getCurrencyId();
        $this->model->rate = $dto->getRate();
        $this->model->rate_date = $dto->getRateDate();

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
        $model = $this->model->findFirst(
            [
                'currency_base_id = ' . $dto->getCurrencyBaseId(),
                'currency_id = ' . $dto->getCurrencyId(),
                'currency_id = ' . $dto->getRateDate(),
            ]
        );

        $this->model->rate = $dto->getRate();

        if (!$model->update()) {
            throw new Exception('Error: ' . implode($this->model->getMessages(), ' | '));
        }

        return $dto->transform($model);
    }
}
