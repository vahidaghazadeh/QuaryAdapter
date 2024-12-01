<?php

namespace Opsource\QueryAdapter\Jobs;

use Closure;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;

class ClosureBuilder
{
    protected $closure;

    public function __construct($closure)
    {
        $this->closure = $closure;
    }

    /**
     * @throws PhpVersionNotSupportedException
     */
    public static function create($closure): ClosureBuilder
    {
        return new self(
            new SerializableClosure(
                Closure::bind(
                    fn () => $this->getClosure()(),
                    new self(fn () => $closure),
                    self::class
                )
            )
        );
    }


    public function getClosure()
    {
        return $this->closure;
    }

    public function __serialize(): array
    {
        return [
            'closure' => serialize($this->closure)
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->closure = unserialize($data['closure'])->getClosure();
    }
}
