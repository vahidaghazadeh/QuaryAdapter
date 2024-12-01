<?php

namespace Opsource\QueryAdapter\Support;

class multipleCommands
{
    private array $stack;

    public function __construct()
    {
        $this->stack = [];
    }

    public function push($item): void
    {
        array_push($this->stack, $item);
    }

    public function pop()
    {
        if (!$this->isEmpty()) {
            return array_pop($this->stack);
        }
        return null;
    }

    public function isEmpty(): bool
    {
        return empty($this->stack);
    }

    public function peek()
    {
        if (!$this->isEmpty()) {
            return end($this->stack);
        }
        return null;
    }
}
