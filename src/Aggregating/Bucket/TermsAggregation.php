<?php

namespace Opsource\QueryAdapter\Aggregating\Bucket;


use Opsource\QueryAdapter\Aggregating\BucketCollection;
use Opsource\QueryAdapter\Aggregating\Result;
use Opsource\QueryAdapter\Contracts\Aggregation;
use Opsource\QueryAdapter\Sorting\Sort;
use Webmozart\Assert\Assert;

class TermsAggregation implements Aggregation
{
    public function __construct(
        private string $name,
        private string $field,
        private ?int $size = null,
        private ?Sort $sort = null,
        private ?Aggregation $composite = null,
    ) {
        Assert::stringNotEmpty(trim($name));
        Assert::stringNotEmpty(trim($field));
        Assert::nullOrGreaterThan($this->size, 0);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toDSL(): array
    {
        $body = ['field' => $this->field];

        if ($this->size !== null) {
            $body['size'] = $this->size;
        }

        if ($this->sort) {
            $body['order'] = $this->sort->toDSL();
        }

        $dsl = [
            $this->name => [
                'terms' => $body,
            ],
        ];

        if ($this->isComposite()) {
            $dsl[$this->name]['aggs'] = $this->composite->toDSL();
        }

        return $dsl;
    }

    public function parseResults(array $response): array
    {
        $buckets = array_map(
            function (array $bucket) {
                $values = $this->isComposite() ? $this->composite->parseResults($bucket) : [];

                return Result::parseBucket($bucket, $values);
            },
            $response[$this->name]['buckets'] ?? []
        );

        return [$this->name => new BucketCollection($buckets)];
    }

    public function isComposite(): bool
    {
        return isset($this->composite);
    }
}
