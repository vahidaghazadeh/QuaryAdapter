<?php

namespace Opsource\QueryAdapter\Support;

class BuildPath
{
    private $path;
    private $build;
    private $namespace;
    private $default;

    public function __construct($config)
    {
        if (is_array($config)) {
            $this->path      = $config['path'];
            $this->build  = $config['build'];
            $this->default  = $config['default'];
            $this->namespace = $config['namespace'] ?? $this->convertPathToNamespace($config['path']);
            return;
        }

        $this->path      = $config;
        $this->build  = (bool) $config;
        $this->namespace = $config;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function generate(): bool
    {
        return $this->build;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    private function convertPathToNamespace($path): array|string
    {
        return str_replace('/', '\\', ltrim($path, config('query_adapter.paths.directives_folder', '')));
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param  mixed  $default
     */
    public function setDefault($default): void
    {
        $this->default = $default;
    }
}
