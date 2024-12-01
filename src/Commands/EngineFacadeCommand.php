<?php

namespace Opsource\QueryAdapter\Commands;

use Illuminate\Support\Str;
use Module;
use Opsource\QueryAdapter\Support\BuilderConfigReader;
use Opsource\QueryAdapter\Support\GeneratorCommand;
use Opsource\QueryAdapter\Support\StubBuilder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class EngineFacadeCommand extends GeneratorCommand implements QueryAdapterCommandBuilderIfc
{
    protected $argumentName = 'facade';
    protected $signature = 'engine:make-facade {model} {module}';

    protected $description = 'Make query adapter search engine model directive';

    public function getDestinationFilePath(): string
    {
        $path = Module::getModulePath($this->getModuleName());
        $facadePath = BuilderConfigReader::read('facade');
        return "$path{$facadePath->getPath()}/{$this->getFileName()}.php";
    }

    public function getTemplateContents()
    {

        $facadePath = str_replace('/', '\\', config('query_adapter.paths.facades_folder'));
//        $classNamespace = "{$this->laravel['modules']->config('namespace')}\\{$this->getModuleName()}\\$facadePath\\{$this->getFileName()}::class";
//
//        $config = \Opsource\QueryAdapter\DirectivesServiceProvider::getDirectives()->merge([
//            $classNamespace
//        ])->toArray();
//
//        Config::set('query_adapter.facades', $config);
        return (new StubBuilder($this->getStubName(), [
            'namespace' => "{$this->laravel['modules']->config('namespace')}\\{$this->getModuleName()}\\$facadePath",
            'FACADE_NAME' => $this->getFileName(),
            'FACADE_ALIAS' => $this->getFileName(),
        ]))->render();
    }
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['module', InputArgument::REQUIRED, 'Module name is mandatory.'],
            ['index', InputArgument::REQUIRED, 'Index is mandatory.'],
        ];
    }

    public function getModelName()
    {
        return $this->argument('model');
    }

    public function getDefaultNamespace(): string
    {
        $engine = $this->laravel['query_builder'];
        return $engine->config('query_adapter.builder.directive.namespace') ?: $engine->config('query_adapter.builder.directive.path', 'Engine/Directives');
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return Str::studly($this->argument('model').config('query_adapter.paths.facade_prefix'));
    }

    protected function getStubName(): string
    {
        return '/facade/engine-facade.stub';
    }

    public function getIndicator(): string
    {
        return "";
    }
}
