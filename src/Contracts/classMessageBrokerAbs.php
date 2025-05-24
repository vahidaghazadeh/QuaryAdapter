<?php

namespace Opsource\QueryAdapter\Contracts;

use AllowDynamicProperties;
use Opsource\Rabbitmq\PublishConfig;
use Opsource\Rabbitmq\RabbitMQExchange;
use Opsource\Rabbitmq\RabbitMQManager;
use Opsource\Rabbitmq\RabbitMQMessage;
use Opsource\Rabbitmq\RabbitMQQueue;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[AllowDynamicProperties] abstract class classMessageBrokerAbs
{
    protected string $routingKey;
    protected RabbitMQManager $AMQManager;
    protected AMQPExchangeType $AMQPExchangeType;
    protected RabbitMQMessage $AMQMessage;
    protected RabbitMQExchange $AMQExchange;
    protected PublishConfig $PublishConfig;
    public const string AMQPExchangeTypeDirect = AMQPExchangeType::DIRECT;
    public const string AMQPExchangeTypeFANOUT = AMQPExchangeType::FANOUT;
    public const string AMQPExchangeTypeHEADERS = AMQPExchangeType::HEADERS;
    public const string AMQPExchangeTypeTOPIC = AMQPExchangeType::TOPIC;

    public function __construct()
    {
        $this->rabbitMQManager = new RabbitMQManager(app());
    }

    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    public function setRoutingKey(string $routingKey): void
    {
        $this->routingKey = $routingKey;
    }

    /**
     * @return RabbitMQManager
     */
    protected function getAMQManager(): RabbitMQManager
    {
        return $this->AMQManager;
    }

    /**
     * @param  RabbitMQManager  $AMQManager
     * @return classMessageBrokerAbs
     */
    public function setAMQManager(RabbitMQManager $AMQManager): static
    {
        $this->AMQManager = $AMQManager;
        return $this;
    }

    /**
     * @return AMQPExchangeType
     */
    protected function getAMQPExchangeType(): AMQPExchangeType
    {
        return $this->AMQPExchangeType;
    }

    /**
     * @param  AMQPExchangeType  $AMQPExchangeType
     * @return classMessageBrokerAbs
     */
    public function setAMQPExchangeType(AMQPExchangeType $AMQPExchangeType): static
    {
        $this->AMQPExchangeType = $AMQPExchangeType;
        return $this;
    }

    /**
     * @return RabbitMQMessage
     */
    protected function getAMQMessage(): RabbitMQMessage
    {
        return $this->AMQMessage;
    }

    /**
     * @param  RabbitMQMessage  $AMQMessage
     * @return classMessageBrokerAbs
     */
    public function setAMQMessage(RabbitMQMessage $AMQMessage): static
    {
        $this->AMQMessage = $AMQMessage;
        return $this;
    }

    /**
     * @return RabbitMQExchange
     */
    protected function getAMQExchange(): RabbitMQExchange
    {
        return $this->AMQExchange;
    }

    /**
     * @param  RabbitMQExchange  $AMQExchange
     * @return classMessageBrokerAbs
     */
    public function setAMQExchange(RabbitMQExchange $AMQExchange, string $exchange): static
    {
        $this->AMQExchange = new RabbitMQExchange('/', $this->getAMQPExchangeType());
        return $this;
    }

    public function getPublishConfig(): PublishConfig
    {
        return $this->PublishConfig;
    }

    public function setPublishConfig(PublishConfig $PublishConfig): static
    {
        $this->PublishConfig = new PublishConfig(
            ['exchange' => ['type' => $this->AMQPExchangeType], 'delivery_mode' => 2]
        );
        //        $publisher->publish($message, $routingKey, $connectionName, $publishConfig);
        $this->rabbitMQManager->publisher()->publish(
            $this->getAMQMessage(),
            $this->getRoutingKey(),
            '',
            $this->getPublishConfig()
        );

        return $this;
    }

    public function getConfig()
    {
        $config = config('query_adapter.queue.default');
        return match ($config) {
            'default' => 'default',
            'rabbitmq' => 'rabbitmq'
        };
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getDefaultBroker()
    {
        return app()->get('config');
    }

    abstract public function getClient();

    abstract public function getConnections();

    abstract public function getChannels();

    protected function fire(RabbitMQQueue $queue, RabbitMQMessage $message): array
    {
        $publisher = $this->rabbitMQManager->publisher();
        $message->setExchange($this->getAMQExchange());
        $routingKey = $queue;
        $connectionName = '';
        $publishConfig = new PublishConfig(['exchange' => ['type' => AMQPExchangeType::DIRECT], 'delivery_mode' => 2]);
//        $publisher->publish($message, $routingKey, $connectionName, $publishConfig);
        return array(
            "status" => "success",
        );
    }
}
