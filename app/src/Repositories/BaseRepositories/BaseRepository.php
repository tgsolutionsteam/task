<?php

namespace App\Repositories\BaseRepositories;

use App\Models\Interfaces\DAOInterface;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Traits\ValidationTrait;
use Exception;
use Phalcon\Acl\Component;
use Phalcon\Mvc\Model\Row;

class BaseRepository extends Component implements BaseRepositoryInterface
{
    protected $model;
    protected $offset = 0;
    protected $limit = 1000;

    public function __construct(DAOInterface $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkComparisonOperator(string $operator)
    {
        $valid_operators = [
            'eq' => '=',
            'gt' => '>',
            'lt' => '<',
            'gte' => '>=',
            'lte' => '<=',
            'not' => '<>',
            'like' => 'like',
            'notin' => 'not in',
            'in' => 'in',
            'is'  => 'is'
        ];

        return array_key_exists($operator, $valid_operators) ? $valid_operators[$operator] : false;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildStringQuery(array $query): string
    {
        $stringQuery = " 1=1 ";
        foreach ($query as $operatorKey => $operatorValues) {
            $operator = $this->checkComparisonOperator($operatorKey);
            if (!$operator) {
                return "";
            }
            foreach ($operatorValues as $operatorValue) {
                foreach ($operatorValue as $field => $value) {
                    $value = addslashes($value);
                    if ($operator === "like") {
                        $stringQuery .= " AND " . $field . " " . $operator . " '%" . $value . "%'";
                        continue;
                    }
                    if ($operator === "not in" || $operator === "in") {
                        $value = implode("','", explode(";", $value));
                        $stringQuery .= " AND " . $field . " " . $operator . " ('" . $value . "')";
                        continue;
                    }
                    if ($operator === "is") {
                        $value = implode("','", explode(";", $value));
                        $stringQuery .= " AND " . $field . " " . $operator . " " . $value;
                        continue;
                    }

                    $stringQuery .= " AND " . $field . $operator . "'" . $value . "'";
                }
            }
        }

        return $stringQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * {@inheritdoc}
     */
    public function findFirst(int $id): Row
    {
        $primaryKey = $this->model->getPrimaryKey() ?? "id";
        $findParams = ["{$primaryKey} = '${id}'"];
        $fields = $this->model->getColumns();
        if ($fields) {
            $findParams['columns'] = $fields;
        }

        $model = $this->model->findFirst($findParams);

        if (!$model) {
            throw new Exception('No data found.');
        }

        return $model;
    }

    /**
     * @query must be an array like ['operator'][] => ['field' => 'value']
     * {@inheritdoc}
     */
    public function find(array $query, array $params = null): array
    {

        $stringQuery = $this->buildStringQuery($query);

        $sort = $params['sort'] ?? null;
        $fields = $params['fields'] ?? null;
        $source = $params['source'] ?? true;

        if (isset($fields) && !$this->isFieldsValid($fields)) {
            throw new Exception('Invalid fields or operators');
        }

        $findParams = [
            'conditions' => $stringQuery,
            'limit' => $this->limit,
            'offset' => $this->offset
        ];

        $fields = $this->model->getColumns((!empty($fields) ? explode(',', $fields) : []));
        if (!empty($fields)) {
            $findParams['columns'] = $fields;
        }

        if (!empty($sort)) {
            $findParams['order'] = $sort;
        }

        $results = [];
        if ($source === true) {
            $results = $this->model->find($findParams);
        }

        $resultsFiltered = $this->model->count(['conditions' => $stringQuery]);
        $resultsTotal    = $this->model->count();
        return [
            'status_code'     => 201,
            'resultsFiltered' => $resultsFiltered,
            'resultsTotal'    => $resultsTotal,
            'data'            => $results,
        ];
    }

    public function isFieldsValid(string $fields): bool
    {
        $metaData = $this->model->getModelsMetaData();
        $attributes = $metaData->getAttributes(
            $this->model
        );

        $currentFields = explode(',', $fields);
        $invalidFields = array_diff($currentFields, $attributes);

        if (count($invalidFields) > 0) {
            return false;
        }

        return true;
    }
}
