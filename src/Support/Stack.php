<?php

namespace Opsource\QueryAdapter\Support;

use ArgumentCountError;
use ArrayAccess;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

class Stack
{
    private array $stack;
    private array $search;
    private bool $hasSearch = false;
    private $closure;

    public function __construct()
    {
        $this->stack = [];
    }

    public function push($item): static
    {
        $this->stack[] = $item;
        return $this;
    }

    public function pop()
    {
        if (!$this->isEmpty()) {
            return array_pop($this->stack);
        }
        return null;
    }

    public function isEmpty(): bool
    {
        return empty($this->stack);
    }

    public function peek()
    {
        if (!$this->isEmpty()) {
            return end($this->stack);
        }
        return null;
    }

    public function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * If the given value is not an array and not null, wrap it in one.
     *
     * @param  mixed  $value
     * @return array
     */
    public function wrap($value)
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }


    /**
     * Run a map over each of the items in the array.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     */
    public function map(array $array, callable $callback)
    {
        $keys = array_keys($array);

        try {
            $items = array_map($callback, $array, $keys);
        } catch (ArgumentCountError) {
            $items = array_map($callback, $array);
        }

        return array_combine($keys, $items);
    }

    /**
     * Select an array of values from an array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    public function select(array|string $keys)
    {
        $keys = $this->wrap($keys);

        return $this->map($this->stack, function ($item) use ($keys) {
            $result = [];

            foreach ($keys as $key) {
                if ($this->accessible($item) && $this->exists($item, $key)) {
                    $result[$key] = $item[$key];
                } elseif (is_object($item) && isset($item->{$key})) {
                    $result[$key] = $item->{$key};
                }
            }

            return $result;
        });
    }

    public function exists($array, string|int $key)
    {
        if ($array instanceof Enumerable) {
            return $array->has($key);
        }

        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        if (is_float($key)) {
            $key = (string) $key;
        }

        return array_key_exists($key, $array);
    }

    public function search(?string $needle = null): mixed
    {
        if ($needle) {
            $this->search = $this->select($needle) ?? [];
            $this->hasSearch = true;
            return $this;
        } else {
            return $this->stack; // Return the entire stack if no needle is provided
        }
    }

    public function get(Closure $closure = null): mixed
    {
        if ($this->hasSearch) {
            return $this->serach;
        } else {
            return $this->stack;
        }
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  iterable  $array
     * @return array
     */
    public static function collapse(iterable $array): array
    {
        $results = [];

        foreach ($array as $values) {
            if ($values instanceof Collection) {
                $values = $values->all();
            } elseif (! is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }
}
