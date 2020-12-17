<?php

namespace App\Controllers\BaseControllers;

use App\Controllers\Interfaces\BaseCUDControllerInterface;
use App\DTO\Interfaces\DTOInterface;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Phalcon\Mvc\Controller;

abstract class BaseCUDController extends Controller implements BaseCUDControllerInterface
{
    public BaseRepositoryInterface $repository;
    public DTOInterface $dto;

       /**
     * {@inheritdoc}
     */
    public function create(): array
    {
        $this->dto = $this->repository->create($this->dto);

        if ($this->dto) {
            return [
                'status_code' => 201,
                'message' => 'Created successfully',
                'data' => [
                    'new_id' => $this->dto->getId(),
                ]
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id): array
    {
        $this->dto->setId($id);
        $this->dto = $this->repository->update($this->dto);
        if ($this->dto) {
            return [
                'status_code' => 201,
                'message' => 'Updated successfully',
                'data' => [
                    'id' => $this->dto->getId(),
                ]
            ];
        }
    }
}
