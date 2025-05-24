<?php

namespace Opsource\QueryAdapter\Traits;

trait Path
{
    protected function basePath(): string
    {
        return '';
    }

    protected function absolutePath(string $path): string
    {
        $basePath = trim($this->basePath());
        $path = trim($path);

        return match (true) {
            $basePath === '' => $path,
            $path === '' => $basePath,
            default => "$basePath.$path"
        };
    }
}
