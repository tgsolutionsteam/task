<?php

namespace App\Services;

use AMQPChannel;
use AMQPConnection;
use AMQPExchange;
use AMQPQueue;
use App\Models\Interfaces\DAOInterface;
use stdClass;

class NotifierService implements NotifierServiceInterface
{
    public $currencies;
    public $connection;
    public function __construct(DAOInterface $currencies)
    {
        $this->currencies = $currencies;
    }

    public function notify(DAOInterface $model, string $routingKey, string $strictSymbol = null)
    {
        $this->connect();

        $channel = new AMQPChannel($this->connection);
        $exchange = new AMQPExchange($channel);

        $queue = new AMQPQueue($channel);
        $queue->setName($routingKey);
        $queue->setFlags(AMQP_NOPARAM);
        $queue->declareQueue();

        $message = $this->getMessage($model);
        if ($strictSymbol === $message->base || $strictSymbol === null) {
            $exchange->publish(json_encode($message), $routingKey);
        }

        $this->connection->disconnect();
    }

    private function connect()
    {
        $this->connection = new AMQPConnection();
        $this->connection->setHost(
            array_key_exists(
                'RABBIT_HOST',
                $_SERVER
            ) ?
                $_SERVER['RABBIT_HOST'] :
                'rabbitmq.dockery'
        );

        $this->connection->setLogin('guest');
        $this->connection->setPassword('guest');
        $this->connection->connect();
    }

    private function getMessage(DAOInterface $model): object
    {
        $message = new stdClass();

        $result = $this->currencies->findFirst('id="' . $model->currency_base_id . '"');
        $message->base = $result->symbol;

        $result = $this->currencies->findFirst('id="' . $model->currency_id . '"');
        $message->symbol = $result->symbol;

        $message->rate_date = $model->rate_date;
        $message->rate = $model->rate;

        return $message;
    }
}
