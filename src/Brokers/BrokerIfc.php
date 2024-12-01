<?php

namespace Opsource\QueryAdapter\Brokers;

interface BrokerIfc
{
    public function init(...$arguments): void;

    public function getMessage();
}
