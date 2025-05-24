<?php

namespace Opsource\QueryAdapter\Tools;

use Opsource\QueryAdapter\Contracts\DSL;
use Webmozart\Assert\Assert;

class Utils implements DSL
{
    public function __construct(
        private string $lang = UtilBase::PAINLESS,
        private array $params = [],
        private ?string $source = null,
        private ?string $id = null,
    ) {
        Assert::oneOf($lang, UtilBase::cases());
        Assert::notNull($source ?? $id);
    }

    public function addParam(string $name, mixed $value): self
    {
        $this->params[$name] = $value;

        return $this;
    }

    public function toDSL(): array
    {
        $dsl = [
            'lang' => $this->lang,
            'params' => $this->params,
        ];

        if ($this->source) {
            $dsl['source'] = $this->source;
        } elseif ($this->id) {
            $dsl['id'] = $this->id;
        }

        return $dsl;
    }
}
