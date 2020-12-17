<?php

namespace App\DTO\DTOs;

use App\DTO\Interfaces\DTOInterface;
use App\DTO\Interfaces\DataTransformerObjectConverterInterface;
use Phalcon\Http\RequestInterface;
use stdClass;

class DataTransformerObjectConverter implements DataTransformerObjectConverterInterface
{
    private DTOInterface $transformer;

    public function __construct(DTOInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(RequestInterface $request): DTOInterface
    {
        $body = new stdClass();
        if (!$request->isGet()) {
            $body = $request->getJsonRawBody();
        }

        $queryString = $request->getQuery();

        return $this->transformer->convert($queryString, $body);
    }
}
