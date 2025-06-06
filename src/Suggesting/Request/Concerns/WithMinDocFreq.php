<?php

namespace Opsource\QueryAdapter\Suggesting\Request\Concerns;

trait WithMinDocFreq
{
    protected ?int $minDocFreq = null;

    public function minDocFreq(int $minDocFreq): static
    {
        $this->minDocFreq = $minDocFreq;

        return $this;
    }
}
