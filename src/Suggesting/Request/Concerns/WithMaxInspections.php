<?php

namespace Opsource\QueryAdapter\Suggesting\Request\Concerns;

trait WithMaxInspections
{
    protected ?int $maxInspections = null;

    public function maxInspections(int $maxInspections): static
    {
        $this->maxInspections = $maxInspections;

        return $this;
    }
}
