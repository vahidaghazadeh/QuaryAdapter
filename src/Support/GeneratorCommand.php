<?php

namespace Opsource\QueryAdapter\Support;

use Illuminate\Console\Command;
use Opsource\QueryAdapter\Exceptions\FileExistsException;

abstract class GeneratorCommand extends Command
{
    /**
     * The name of 'name' argument.
     *
     * @var string
     */
    protected $argumentName = '';
    protected bool $multiple = false;
    protected bool $hasModule = true;
    protected multipleCommands $multipleCommands;

    public function __construct()
    {
        parent::__construct();
        $this->multipleCommands = new multipleCommands();
    }

    /**
     * Get template contents.
     *
     * @return string
     */
    abstract protected function getTemplateContents();

    /**
     * Get the destination file path.
     *
     * @return string
     */
    abstract protected function getDestinationFilePath();

    public function handle(): int
    {
        $path = str_replace('\\', '/', $this->getDestinationFilePath());

        if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }

        $contents = $this->getTemplateContents();
        $this->validateModule();
        try {
            $this->components->task("Build {$path} {$this->argumentName}", function () use ($path, $contents) {
                $overwriteFile = $this->hasOption('force') ? $this->option('force') : false;
                (new ClassBuilder($path, $contents))->withFileOverwrite($overwriteFile)->build();
            });
        } catch (FileExistsException $e) {
            $this->components->error("File: {$path} exist");
            return E_ERROR;
        }
        return 0;
    }



    public function getModuleName(): string
    {

        $module = $this->argument('module') ?: app('modules')->getUsedNow();

        $module = app('modules')->findOrFail($module);

        return $module->getStudlyName();
    }

    public function getModuleNameSpace(): string
    {
        return "{$this->laravel['modules']->config('namespace')}\\{$this->getModuleName()}";
    }

    public function getClass(): string
    {
        return class_basename($this->argument($this->argumentName));
    }

    public function getDefaultNamespace(): string
    {
        return '';
    }

    public function getClassNamespace($module): string
    {
        $extra = str_replace($this->getClass(), '', $this->argument($this->argumentName));

        $extra = str_replace('/', '\\', $extra);

        $namespace = $this->laravel['modules']->config('namespace');

        $namespace .= '\\'.$module->getStudlyName();

        $namespace .= '\\'.$this->getDefaultNamespace();

        $namespace .= '\\'.$extra;

        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }

    protected function validateModule()
    {
        if ($this->hasModule) {
            if (!$this->laravel['modules']->getModulePath($this->getModuleName())) {
                $this->error("Module {$this->getModuleName()} does not exist!");
                return 0;
            }
        }
    }

    protected function runMultipleCommand()
    {
//        if(!$this->multipleCommands->isEmpty()) {
//            $command = $this->multipleCommands->peek();
//            $this->components->task("Build {$command['command']}", function () use ($path, $contents) {
//                $overwriteFile = $this->hasOption('force') ? $this->option('force') : false;
//                (new ClassBuilder($path, $contents))->withFileOverwrite($overwriteFile)->build();
//            });
//
//            return $this->call($command['command'], $command['arguments']);
//        }
        return false;
    }
}
