<?php

namespace Opsource\QueryAdapter\Search;


use Opsource\QueryAdapter\Contracts\DSL;

class Cursor implements DSL
{
    public function __construct(private array $parts)
    {
    }

    public function isBOF(): bool
    {
        return !$this->parts;
    }

    public function toDSL(): array
    {
        return array_values($this->parts);
    }

    public function encode(): string
    {
        return base64_encode(json_encode($this->parts, JSON_THROW_ON_ERROR));
    }

    public function keys(): array
    {
        return array_keys($this->parts);
    }

    /**
     * @throws \JsonException
     */
    public static function decode(?string $source): ?static
    {
        return blank($source)
            ? null
            : new Cursor(json_decode(base64_decode($source), true, 512, JSON_THROW_ON_ERROR));
    }

    public static function BOF(): static
    {
        return new static([]);
    }
}
