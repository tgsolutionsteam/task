<?php

namespace App\Repositories\Interfaces;

use App\DTO\Interfaces\DTOInterface;

interface CUDRepositoryInterface
{
    /**
     * Create a row in database
     * @param DTOInterface $dto
     * @return DTOInterface|null
     */
    public function create(DTOInterface $dto): ?DTOInterface;

    /**
     * Update a row in database
     * @param DTOInterface $dto
     * @return DTOInterface|null
     */
    public function update(DTOInterface $dto): ?DTOInterface;
}
