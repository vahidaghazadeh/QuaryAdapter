<?php

namespace Opsource\QueryAdapter\Suggesting;

use Opsource\QueryAdapter\Contracts\SearchIndex;
use Illuminate\Support\Collection;
use Opsource\QueryAdapter\Suggesting\Request\PhraseSuggester;
use Opsource\QueryAdapter\Suggesting\Request\TermSuggester;

class SuggestQuery
{
    protected ?string $text = null;
    protected SuggesterCollection $suggesters;

    public function __construct(protected SearchIndex $index)
    {
        $this->suggesters = new SuggesterCollection();
    }

    public function term(string $name, string $field): TermSuggester
    {
        $suggester = new TermSuggester($name, $field);
        $this->suggesters->add($suggester);

        return $suggester;
    }

    public function phrase(string $name, string $field): PhraseSuggester
    {
        $suggester = new PhraseSuggester($name, $field);
        $this->suggesters->add($suggester);

        return $suggester;
    }

    //region Executing
    public function get(): Collection
    {
        $response = $this->execute();

        return $this->suggesters->parseResults($response['suggest'] ?? []);
    }

    protected function execute(): array
    {
        $dsl = [
            'suggest' => $this->suggesters->toDSL(),
        ];

        if ($this->text) {
            $dsl['suggest']['text'] = $this->text;
        }

        return $this->index->search(array_filter($dsl));
    }

    //endregion

    //region Customization
    public function globalText(string $text): static
    {
        $this->text = $text;

        return $this;
    }
    //endregion
}
