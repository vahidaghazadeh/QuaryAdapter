<?php

namespace App\Packages\Opsource\QueryAdapter\src\Filters\Indicators;

use Opsource\QueryAdapter\Contracts\IndicatorIfc;

class WithRouting implements IndicatorIfc
{
    const KEY_ROUTING = '_routing';
    const KEY_FIELD = 'field';
    const KEY_CHILD = 'child';

    public function __construct(protected array $more, protected string $field, public array $values = [])
    {
    }

    public function toDSL(): array
    {
        $key = $this->more['key'];
        if ($key === self::KEY_ROUTING) {
            return [
                'term' => [
                    "_routing" => $this->more['routing_id'],
                ]
            ];
        }

        if ($key === self::KEY_CHILD) {
            return [
                'parent_id' => [
                    'type' => $this->field,
                    'id' => $this->more['routing_id']
                ]
            ];
        }

        if ($key === self::KEY_FIELD) {
            return [
                'terms' => [
                    $this->field => $this->values,
                ]
            ];
        }

        throw new \InvalidArgumentException("Invalid key: {$key}");
    }
}
