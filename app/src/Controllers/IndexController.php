<?php

namespace App\Controllers;

use App\Controllers\BaseControllers\BaseQueryController;
use App\Models\ViewCurrenciesRates;
use App\Repositories\Repositories\CurrenciesRatesRepository;
use App\Services\ExchangeRatesService;
use Exception;
use Milo\XmlRpc\Converter;
use Milo\XmlRpc\MethodCall;
use SimpleXMLElement;

class IndexController extends BaseQueryController
{

    public $xmlParser;
    /**
     * {@inheritdoc}
     */
    public function onConstruct()
    {
        $this->repository = new CurrenciesRatesRepository(new ViewCurrenciesRates());
        $this->serviceExchangeRates = new ExchangeRatesService();
        $this->xmlParser = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><root></root>');
    }

    public function index()
    {
        if ($this->request->getContentType() != 'text/xml') {
            throw new Exception('Invalid Headers');
        }

        $xml = $this->request->getRawBody();
        $converter = new Converter();
        $call = $converter->fromXml($xml);
        if (!$call instanceof MethodCall) {
            throw new Exception('Error! Expected method call. Got ' . get_class($call));
        }

        if (!method_exists($this, $call->getName())) {
            throw new Exception('Error! This method is unavaiable.');
        }

        $params = $call->getParameters();
        $result = $this->{$call->getName()}($params);
        $this->recursiveWalk(json_decode(json_encode($result['data'])));
        return $this->xmlParser->asXML();
    }

    private function recursiveWalk(array $walk)
    {
        foreach ($walk as $key => $value) {
            if (is_array($value)) {
                $this->recursiveWalk($value);
            }

            if (is_object($value)) {
                $this->recursiveWalk((array) $value);
            }

            if (is_string($value)) {
                $this->xmlParser->addChild($key, $value);
            }
        }
    }

    public function getRates(array $params = null)
    {
        $base = $params[0] ?? 'EUR';
        $rates = [];
        $rates['base'] = $base;
        $this->arrWhere['eq'][] = [ 'base_symbol' => $base ];
        if (isset($params[1])) {
            $this->arrWhere['gte'][] = [ 'rate_date' => $params[1] ];
            $rates['start_at'] = $params[1];
        }

        if (isset($params[2])) {
            $this->arrWhere['lte'][] = [ 'rate_date' => $params[2] ];
            $rates['start_at'] = $params[2];
        }

        $data = $this->repository->find($this->arrWhere);
        if ($data['resultsFiltered'] === 0) {
            $this->serviceExchangeRates->getRates($rates);
            $data = $this->repository->find($this->arrWhere);
        }

        return $data;
    }
}
