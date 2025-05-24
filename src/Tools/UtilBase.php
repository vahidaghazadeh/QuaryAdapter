<?php

namespace Opsource\QueryAdapter\Tools;

final class UtilBase
{
    public const PAINLESS = 'painless';
    public const MUSTACHE = 'mustache';

    public static function cases(): array
    {
        return [
            self::PAINLESS,
            self::MUSTACHE,
        ];
    }
}
