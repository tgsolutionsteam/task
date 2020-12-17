<?php

namespace App\DTO\BaseDTOs;

use App\DTO\Interfaces\BaseDTOInterface;
use App\DTO\Interfaces\DTOInterface;
use League\Fractal\TransformerAbstract;
use Exception;
use ReflectionClass;
use stdClass;

class BaseDTO extends TransformerAbstract implements BaseDTOInterface
{
    protected $dto;
    private array $allowedOptions = [
        'sort',
        'columns',
        'source',
        'limit',
        'offset',
    ];

    /**
     * {@inheritdoc}
     */
    public function convert(array $queryString, object $body): DTOInterface
    {

        $reflect = new ReflectionClass($this);
        foreach ($reflect->getProperties() as $property) {
            $this->dto->{$property->getName()} = $body->{$property->getName()} ?? null;
            if (
                $this->dto->{$property->getName()} !== null &&
                gettype($this->dto->{$property->getName()}) !== gettype($body->{$property->getName()})
            ) {
                throw new Exception('Invalid types');
            }
            unset($body->{$property->getName()});
        }

        if (count((array) $body) > 0) {
            throw new Exception('Invalid fields or operators');
        }

        unset($queryString['_url']);

        if ($queryString) {
            $this->dto->queryString = new stdClass();
            foreach ($queryString as $key => $value) {
                if ($reflect->hasProperty($key) && !in_array($key, $this->allowedOptions)) {
                    $this->dto->queryString->$key = $value;
                }
            }
        }

        return $this->dto;
    }
}
