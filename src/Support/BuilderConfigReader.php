<?php

namespace Opsource\QueryAdapter\Support;

class BuilderConfigReader
{
    public static function read(string $value): BuildPath
    {
        return new BuildPath(config("query_adapter.builder.$value"));
    }
}
