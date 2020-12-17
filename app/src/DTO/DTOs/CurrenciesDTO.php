<?php

namespace App\DTO\DTOs;

use App\DTO\BaseDTOs\BaseDTO;
use App\DTO\Interfaces\DTOInterface;

class CurrenciesDTO extends BaseDTO implements DTOInterface
{
    protected ?int $id;
    protected ?string $symbol;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    /**
     * @param string|null $symbol
     */
    public function setSymbol(?string $symbol): void
    {
        $this->symbol = $symbol;
    }


    /**
     * {@inheritdoc}
     */
    public function convert(array $queryString, object $body): CurrenciesDTO
    {
        $this->dto = new CurrenciesDTO();
        return parent::convert($queryString, $body);
    }

    /**
     * {@inheritdoc}
     */
    public function transform($symbol): DTOInterface
    {
        $this->setId($symbol->id);
        $this->setSymbol($symbol->symbol);

        return $this;
    }
}
