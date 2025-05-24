<?php

namespace Opsource\QueryAdapter\Jobs;

use ArgumentCountError;
use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Log;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class ArgsBuilder
{
    public array $stack;
    private array $search = [];
    private bool $hasSearch = false;
    private $closure;
    /**
     * @var true
     */
    private bool $asTotal = false;
    /**
     * @var true
     */
    private bool $asFirst = false;
    /**
     * @var true
     */
    private bool $asValue = false;

    public function __construct()
    {
        $this->stack = [];
    }

    public function push($item, bool $uniq = true): static
    {
        if ($uniq && is_array($item)) {
            $this->replaceOrPush($item);
        } else {
            $this->stack[] = $item;
        }
        return $this;
    }

    private function replaceOrPush(array $item): void
    {
        foreach ($this->stack as &$existingItem) {
            if (is_array($existingItem)) {
                foreach ($item as $key => $value) {
                    if (array_key_exists($key, $existingItem)) {
                        $existingItem[$key] = $value;
                        return; // Assuming one key match per push; remove if multiple replacements are allowed
                    }
                }
            }
        }
        $this->stack[] = $item; // Add new item if no keys match
    }

    public function merge($item, bool $uniq): static
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

    /**
     * @return $this
     */
    public function asTotal(): static
    {
        $this->asTotal = true;
        return $this;
    }


    /**
     * @return $this
     */
    public function asFirst(): static
    {
        $this->asFirst = true;
        return $this;
    }

    /**
     * @param  string  $needle
     * @param  string  $separator
     * @param  int|null  $index
     * @return array|string
     */
    public function explode(string $needle, string $separator, int $index = null): array|string
    {
        $response = explode($separator, $needle);
        if($index && $this->count($response)) {
            return $response[$index];
        }
        return $response;
    }

    public function search(string $needle, bool $_response = false, bool $_global = true): string|array|static
    {
        $response = $this->collapse($this->select($needle)) ?? new Collection();

        if ($this->asValue) {
            $this->asValue = false;
            if (str_contains($needle, '.')) {
                $response = $this->collapse($this->select($this->explode($needle, '.', 0)));
            }
            return $this->value($response, $needle);
        }

        if ($this->asTotal) {
            $this->asTotal = false;
            return $this->count($response);
        }

        if ($this->asFirst) {
            $this->asFirst = false;
            return $this->first($response);
        }

        if ($_response) {
            return $response;
        }

        if ($_global && $this->count($response)) {
            $this->search = $response;
            $this->hasSearch = true;
        }

        return $this;
    }

    public function unique(string|array $keys): array|static
    {
        $keys = $this->wrap($keys);

        return $this->map($this->stack, function ($item) use ($keys) {
            foreach ($keys as $key) {
                if ($this->accessible($item) && $this->exists($item, $key)) {
                    $this->stack[$key] = $item;
                } elseif (is_object($item) && isset($item->{$key})) {
                    $this->stack[$key] = $item->{$key};
                }
            }
        });
    }


    public function collapse(array $array = null): array
    {
        $results = [];
        foreach ($array ?? $this->stack as $values) {
            if ($values instanceof Collection) {
                $values = $values->all();
            } elseif (!is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

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

    public function wrap($value)
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

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

    public function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
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
            $key = (string)$key;
        }

        return array_key_exists($key, $array);
    }

    public function get(bool $collapse = false): array|static
    {
        if ($this->hasSearch) {
            return $collapse ? $this->collapse($this->search) : $this->search;
        } else {
            return $this->stack;
        }
    }

    public function value(array $array, string $key, string $default = null)
    {
        if (!static::accessible($array)) {
            return value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (!str_contains($key, '.')) {
            return $array[$key] ?? value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }
        return $array;
    }

    public function first($array = null, callable $callback = null, $default = null)
    {
        if (!$array && $this->hasSearch) {
            $array = $this->search;
        }

        if (!$array && !$this->hasSearch) {
            $array = $this->stack;
        }

        if (is_null($callback)) {
            if (empty($array)) {
                return value($default);
            }

            foreach ($array as $item) {
                return $item;
            }

            return value($default);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return value($default);
    }

    public function count($array = null): int
    {
        if ($array) {
            return count($array);
        }

        if ($this->hasSearch) {
            return count($this->search);
        }

        return count($this->stack);
    }

    public function asObject(): Collection
    {
        if ($this->search) {
            return collect($this->search);
        }
        return collect($this->stack);
    }

    public function asArray(): array
    {
        if ($this->search) {
            return $this->collapse($this->search);
        }
        return $this->collapse($this->stack);
    }

    public function asValue(): static
    {
        $this->asValue = true;
        return $this;
    }

    public function flatterer($array = null): array
    {
        return iterator_to_array(
            new RecursiveIteratorIterator(
                new
                RecursiveArrayIterator(
                    $array ?? $this->asArray()
                )
            )
        );
    }

    public function forget($key, $array = null): mixed
    {
        if ($array) {
            return data_forget($array, $key);
        }
        $array = $this->flatterer($this->get(true));
        return data_forget($array, $key);
    }

}
