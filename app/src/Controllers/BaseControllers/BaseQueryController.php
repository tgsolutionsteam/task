<?php

namespace App\Controllers\BaseControllers;

use App\Controllers\Interfaces\BaseQueryControllerInterface;
use App\DTO\Interfaces\DTOInterface;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Services\UtilsService;
use Phalcon\Mvc\Controller;

abstract class BaseQueryController extends Controller implements BaseQueryControllerInterface
{
    public BaseRepositoryInterface $repository;
    public DTOInterface $dto;
    public ?array $arrWhere = [];

    /**
     * {@inheritdoc}
     */
    public function list(): array
    {
        $this->populateWhereClause();
        return $this->repository->find($this->arrWhere, $this->getParams());
    }

    /**
     * {@inheritdoc}
     */
    public function show(int $id): array
    {
        $content = $this->repository->findFirst($id);
        return [
            'status_code' => 201,
            'data' => $content
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getParams(): array
    {
        $this->setOptions();
        $sort = $this->request->get('sort', 'string');
        $fields = $this->request->get('columns', 'string');
        $source = $this->request->get('source') != 'false';

        $util = new UtilsService();
        $arrSort = $sort ? $util->formatSort($sort) : null;

        return [
            'sort' => $arrSort,
            'fields' => $fields,
            'source' => $source,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(): void
    {
        $limit = $this->request->get('limit', 'int');
        $offset = $this->request->get('offset', 'int');
        if ($limit) {
            $this->repository->setLimit($limit);
        }

        if ($offset) {
            $this->repository->setOffset($offset);
        }
    }

    protected function populateWhereClause(): void
    {
        if (isset($this->dto->queryString)) {
            foreach ($this->dto->queryString as $key => $clause) {
                $expWhere = explode(',', $clause);
                $operator = isset($expWhere[1]) ? $expWhere[1] : 'eq';
                $this->arrWhere[$operator][] = [$key => $expWhere[0]];
            }
        }
    }
}
