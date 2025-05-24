<?php

namespace Opsource\QueryAdapter\Suggesting\Enums;

final class SuggestMode
{
    public const MISSING = 'missing';
    public const POPULAR = 'popular';
    public const ALWAYS = 'always';

    public static function cases(): array
    {
        return [
            self::MISSING,
            self::POPULAR,
            self::ALWAYS,
        ];
    }
}
