<?php

namespace Opsource\QueryAdapter\Contracts;

use Illuminate\Database\Eloquent\Model;
use Opsource\QueryAdapter\Performers\EngineDirectiveAdapter;

interface SearchableDirectiveInterface
{
    public function setModel(Model $model = null): static;

    public function getIndexName(): string;

    public function getIndicator(): string;

    public function getModel(): Model;

    public function getPreventObserver(): bool;

    public function setPreventObserver(bool $prevent): static;

    public function adapter(): EngineDirectiveAdapter;
}
