<?php

namespace Opsource\QueryAdapter\Brokers;

interface Message
{
    public function setStream(): mixed;

    public function getStream(mixed $stream);

    public function ack($multiple = false);

    public function nack($requeue = false, $multiple = false);

    public function reject($requeue = true);

}
