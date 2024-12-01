<?php

namespace Opsource\QueryAdapter\Brokers;

abstract class BrokerAbs
{
    protected mixed $exchange;
    protected mixed $message;
    protected mixed $publisherConfig;
    protected string $routingKey;
    protected mixed $quote;
    protected mixed $exchangeConfig;
    protected mixed $publisher;

    abstract protected function init(array $arguments): static;

    protected function setMessage($message): static
    {
        $this->message = $message;
        return $this;
    }

    protected function getMessage(): mixed
    {
        return $this->getMessage();
    }

    /**
     * @return mixed
     */
    protected function getPublisherConfig(): mixed
    {
        return $this->publisherConfig;
    }

    protected function getExchange()
    {
        return $this->getExchange();
    }

    protected function setExchange(mixed $exchange): static
    {
        $this->exchange = $exchange;
        return $this;
    }

    /**
     * @return $this
     */
    protected function setPublisherConfig(...$arguments): static
    {
        $this->publisherConfig = $arguments;
        return $this;
    }

    protected function getExchangeConfig(): mixed
    {
        return $this->exchangeConfig;
    }

    protected function setExchangeConfig(array $config): static
    {
        $this->exchangeConfig = $config;
        return $this;
    }

    protected function getRoutingKey(): string
    {
        return $this->routingKey ?? $this->getQuote();
    }

    protected function setRoutingKey(string $routingKey): static
    {
        $this->routingKey = $routingKey;
        return $this;
    }

    /**
     * @param  mixed  $queue
     * @return \static
     */
    protected function setQuote(mixed $queue): static
    {
        $this->quote = $queue;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getQuote(): mixed
    {
        return $this->quote;
    }

    abstract protected function setPublisher(): static;

    abstract protected function getPublisher(): mixed;


    abstract public function publish(): string;
}
