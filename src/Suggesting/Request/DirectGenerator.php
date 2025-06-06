<?php

namespace Opsource\QueryAdapter\Suggesting\Request;


use Opsource\QueryAdapter\Suggesting\Request\Concerns\WithMaxEdits;
use Opsource\QueryAdapter\Suggesting\Request\Concerns\WithMaxInspections;
use Opsource\QueryAdapter\Suggesting\Request\Concerns\WithMaxTermFreq;
use Opsource\QueryAdapter\Suggesting\Request\Concerns\WithMinDocFreq;
use Opsource\QueryAdapter\Suggesting\Request\Concerns\WithMinWordLength;
use Opsource\QueryAdapter\Suggesting\Request\Concerns\WithPrefixLength;
use Opsource\QueryAdapter\Suggesting\Request\Concerns\WithSize;
use Opsource\QueryAdapter\Suggesting\Request\Concerns\WithSuggestMode;

class DirectGenerator
{
    use WithSuggestMode;
    use WithSize;
    use WithMaxEdits;
    use WithPrefixLength;
    use WithMinWordLength;
    use WithMaxInspections;
    use WithMinDocFreq;
    use WithMaxTermFreq;
    protected ?string $preFilter = null;
    protected ?string $postFilter = null;

    public function __construct(protected string $field)
    {
    }

    public function toDSL(): array
    {
        return array_filter([
            "field" => $this->field,
            "size" => $this->size,
            "suggest_mode" => $this->suggestMode,
            "max_edits" => $this->maxEdits,
            "prefix_length" => $this->prefixLength,
            "min_word_length" => $this->minWordLength,
            "max_inspections" => $this->maxInspections,
            "min_doc_freq" => $this->minDocFreq,
            "max_term_freq" => $this->maxTermFreq,
            "pre_filter" => $this->preFilter,
            "post_filter" => $this->postFilter,
        ]);
    }

    public function preFilter(string $preFilter): static
    {
        $this->preFilter = $preFilter;

        return $this;
    }

    public function postFilter(string $postFilter): static
    {
        $this->postFilter = $postFilter;

        return $this;
    }
}
