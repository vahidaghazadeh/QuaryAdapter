<?php

namespace Opsource\QueryAdapter\Tools;

final class SortType
{
    public const NUMBER = 'number';

    public static function cases(): array
    {
        return [
            self::NUMBER,
        ];
    }
}
