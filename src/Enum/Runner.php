<?php

namespace Opsource\QueryAdapter\Enum;

enum Runner: string
{
    case BROKER = 'asBrokerStart';
    case CLOSURE = 'asClosureStart';
    case BUILTIN = 'asBuiltinStart';

    /**
     * @return string
     */
    public function getRunner(): string
    {
        return $this->value;
    }
}
