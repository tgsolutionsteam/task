<?php

namespace App\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class Guzzle
{
    public function apiRequest(string $endpoint, array $params = null): object
    {
        $http = new Client(
            [
                'base_uri' => 'https://api.exchangeratesapi.io/',
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]
        );

        $query = isset($params['query']) ? $params['query'] : null;

        try {
            $request = $http->get(
                $endpoint,
                [
                    'debug' => false,
                    'query' => $query,
                ]
            );
            $result  = $request->getBody();
        } catch (ClientException $e) {
            $result = $e->getResponse()->getBody(true);
        } catch (RequestException $e) {
            $result = '{"message": "' . $e->getMessage() . '"}';
            if ($e->hasResponse()) {
                $result = $e->getResponse()->getBody(true);
            }
        }

        return (object) json_decode($result, true);
    }
}
