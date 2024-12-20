<?php

namespace Opsource\QueryAdapter\Suggesting\Request\Concerns;

trait WithMinWordLength
{
    protected ?int $minWordLength = null;

    public function minWordLength(int $minWordLength): static
    {
        $this->minWordLength = $minWordLength;

        return $this;
    }
}
