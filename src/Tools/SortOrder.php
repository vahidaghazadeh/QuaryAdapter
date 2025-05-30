<?php

namespace Opsource\QueryAdapter\Tools;

final class SortOrder
{
    public const ASC = 'asc';
    public const DESC = 'desc';

    public static function cases(): array
    {
        return [
            self::ASC,
            self::DESC,
        ];
    }
}
