<?php

namespace Opsource\QueryAdapter\Jobs;

use Illuminate\Support\Facades\Log;
use Opsource\QueryAdapter\Brokers\BrokerAbs;
use Opsource\Rabbitmq\PublishConfig;
use Opsource\Rabbitmq\RabbitMQExchange;
use Opsource\Rabbitmq\RabbitMQManager;
use Opsource\Rabbitmq\RabbitMQMessage;
use Opsource\Rabbitmq\RabbitMQQueue;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class AMQManager extends BrokerAbs
{
    protected RabbitMQManager $rabbitMQ;
    public function __construct()
    {
        $this->rabbitMQ = new RabbitMQManager(app());
    }

    public function init(array $arguments): static
    {
        try {
            Log::debug(\json_encode($arguments));
            $this
                ->setMessage(new RabbitMQMessage(json_encode($arguments['message'], JSON_UNESCAPED_UNICODE)))
                ->setExchangeConfig(
                    $config ?? [
                        'type' => AMQPExchangeType::DIRECT,
                        'durable' => true,
                        'declare' => true,
                        'delivery_mode' => 2
                    ]
                )
                ->setExchange(new RabbitMQExchange($arguments['exchange'] ?? $arguments['queue'], $this->getExchangeConfig()))
                ->setRoutingKey($arguments['routing_ket'] ?? $arguments['queue'])->setPublisher()
                ->getMessage();
            $this->setQuote(new RabbitMQQueue($arguments['queue'], ['durable' => $arguments['durable'] ?? true, 'declare' => $arguments['declare'] ?? true]));
            $this->setPublisherConfig(new PublishConfig(['exchange' => $this->getExchangeConfig(), 'queue' => $this->quote]));

        } catch (\Exception|\Error|\Throwable $ex) {
            Log::debug($ex->getMessage());
            return $this;
        }
        return $this;
    }


    protected function getPublisher(): mixed
    {
        return $this->publisher;
    }

    protected function setPublisher(): static
    {
        $this->publisher = $this->rabbitMQ->publisher();
        return $this;
    }

    public function publish(): string
    {
        $this->getPublisher()->publish(
            $this->getMessage(),
            $this->getQuote()->getName(),
            '',
            $this->getPublisherConfig()
        );
        return "success";
    }
}
