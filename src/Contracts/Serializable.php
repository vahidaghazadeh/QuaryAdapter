<?php

namespace Opsource\QueryAdapter\Contracts;

interface Serializable
{
    /* Metodi */
    public function serialize(): ?string;

    public function unserialize(string $data): void;
}
