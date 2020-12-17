<?php

namespace App\DTO\DTOs;

use App\DTO\BaseDTOs\BaseDTO;
use App\DTO\Interfaces\DTOInterface;

class RatesDTO extends BaseDTO implements DTOInterface
{

    public ?int $currencyBaseId;
    public ?int $currencyId;
    public ?string $rateDate;
    public ?float $rate;

    /**
     * @return int|null
     */
    public function getCurrencyBaseId(): ?int
    {
        return $this->currencyBaseId;
    }

    /**
     * @param int|null $currencyBaseId
     */
    public function setCurrencyBaseId(?int $currencyBaseId): void
    {
        $this->currencyBaseId = $currencyBaseId;
    }

    /**
     * @return int|null
     */
    public function getCurrencyId(): ?int
    {
        return $this->currencyId;
    }

    /**
     * @param int|null $currencyId
     */
    public function setCurrencyId(?int $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    /**
     * @return string|null
     */
    public function getRateDate(): ?string
    {
        return $this->rateDate;
    }

    /**
     * @param string|null $rateDate
     */
    public function setRateDate(?string $rateDate): void
    {
        $this->rateDate = $rateDate;
    }

    /**
     * @return float|null
     */
    public function getRate(): ?float
    {
        return $this->rate;
    }

    /**
     * @param float|null $rate
     */
    public function setRate(?float $rate): void
    {
        $this->rate = $rate;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(array $queryString, object $body): RatesDTO
    {
        $this->dto = new RatesDTO();
        return parent::convert($queryString, $body);
    }

    /**
     * @return int|null
     */
    public function getId(): ?string
    {
        return $this->baseCurrencyId . ',' . $this->currency_id;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($rate): DTOInterface
    {
        $this->setCurrencyBaseId($rate->currency_base_id);
        $this->setCurrencyId($rate->currency_id);
        $this->setRate($rate->rate);
        $this->setRateDate($rate->rate_date);

        return $this;
    }
}
