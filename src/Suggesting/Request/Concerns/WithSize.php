<?php

namespace Opsource\QueryAdapter\Suggesting\Request\Concerns;

trait WithSize
{
    protected ?int $size = null;

    public function size(int $size): static
    {
        $this->size = $size;

        return $this;
    }
}
