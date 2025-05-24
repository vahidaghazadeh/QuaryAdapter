<?php

namespace Opsource\QueryAdapter\Support;

class StubBuilder
{
    protected static ?string $basePath = null;

    public function __construct(protected string $path, protected array $replaces) {}

    public static function create(string $path, array $replaces = []): static
    {
        return new static($path, $replaces);
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        $path = static::getBasePath().$this->path;

        return file_exists($path) ? $path : __DIR__.'/stubs'.$this->path;
    }

    public static function setBasePath($path): void
    {
        static::$basePath = $path;
    }

    public function getReplaces(): array
    {
        return $this->replaces;
    }

    public function setReplaces(array $replaces): static
    {
        $this->replaces = $replaces;

        return $this;
    }

    /**
     * Get base path.
     */
    public static function getBasePath(): ?string
    {
        return static::$basePath;
    }

    /**
     * Get stub contents.
     */
    public function getContents(): string|array|false
    {
        $contents = file_get_contents($this->getPath());

        foreach ($this->getReplaces() as $search => $replace) {
            $contents = str_replace('{'.strtoupper($search).'}', $replace, $contents);
        }

        return $contents;
    }

    /**
     * Get stub contents.
     */
    public function render(): false|array|string
    {
        return $this->getContents();
    }

    /**
     * Save stub to specific path.
     */
    public function saveTo(string $path, string $filename): bool
    {
        return file_put_contents($path.'/'.$filename, $this->getContents());
    }

    /**
     * Set replacements array.
     *
     *
     * @return $this
     */
    public function replace(array $replaces = []): static
    {
        $this->replaces = $replaces;

        return $this;
    }

    /**
     * Handle magic method __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
