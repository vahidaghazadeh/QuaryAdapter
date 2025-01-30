<?php

namespace Opsource\QueryAdapter\Commands;

use Illuminate\Support\Str;
use Module;
use Opsource\QueryAdapter\Support\BuilderConfigReader;
use Opsource\QueryAdapter\Support\GeneratorCommand;
use Opsource\QueryAdapter\Support\StubBuilder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class EngineModelDirectorCommand extends GeneratorCommand implements QueryAdapterCommandBuilderIfc
{
    protected $argumentName = 'directive';

    //    protected $signature = 'engine:make {name} {model} --{module} --{force=}';
    protected $signature = 'engine:make-directive {model} {module} {index} {--indicator} {--facade} {--job} {--broker}';

    protected $description = 'Make query adapter search engine model directive';

    public function getDestinationFilePath(): string
    {
        $path = Module::getModulePath($this->getModuleName());
        $directivePath = BuilderConfigReader::read('directive');

        return "$path{$directivePath->getPath()}/{$this->getFileName()}.php";
    }

    public function getTemplateContents(): array|false|string
    {
        $directivesPath = str_replace('/', '\\', config('query_adapter.paths.directives_folder'));

        //        $classNamespace = "{$this->laravel['modules']->config('namespace')}\\{$this->getModuleName()}\\$directivesPath\\{$this->getFileName()}::class";
        //        $config = \Opsource\QueryAdapter\DirectivesServiceProvider::getDirectives()->merge([
        //            $classNamespace
        //        ])->toArray();
        //        Config::set('query_adapter.directives', $config);

        if ($this->option('facade')) {
            $this->call('engine:make-facade', [
                'model' => $this->getModelName(),
                'module' => $this->getModuleName(),
            ]);
        }

        if ($this->option('job')) {
            $params = [
                'model' => $this->getModelName(),
                'module' => $this->getModuleName(),
                'name' => $this->argument('model'),
            ];
            if ($this->option('broker')) {
                $params['--broker'] = $this->option('broker');
            }
            $this->call('engine:make-job', $params);
        }

        return (new StubBuilder($this->getStubName(), [
            'namespace' => "{$this->laravel['modules']->config('namespace')}\\{$this->getModuleName()}\\$directivesPath",
            'model_namespace' => "{$this->laravel['modules']->config('namespace')}\\{$this->getModuleName()}\\Models\\{$this->getModelName()}",
            'directive_name' => $this->getFileName(),
            'model_name' => $this->getModelName(),
            //            "model_instance" => lcfirst($this->getModelName()),
            'index' => $this->getIndex(),
            'indicator' => $this->getIndicator(),
        ]))->render();
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['model', InputArgument::REQUIRED, 'Model name is mandatory.'],
            ['module', InputArgument::REQUIRED, 'Module name is mandatory.'],
            ['index', InputArgument::REQUIRED, 'Index is mandatory.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['--indicator', 'i', InputOption::VALUE_NONE, 'Search engine indicator.', null],
            ['--force', null, InputOption::VALUE_NONE, 'Rewrite file'],
            ['--facade', null, InputOption::VALUE_NONE, 'Make Facade', null],
        ];
    }

    public function getModelName()
    {
        return $this->argument('model');
    }

    public function getIndex()
    {
        return $this->argument('index');
    }

    public function getIndicator(): string
    {
        return $this->option('indicator') ?: 'id';
    }

    private function geIndicatorNameWithoutNamespace(): string
    {
        return class_basename($this->getModelName());
    }

    public function getDefaultNamespace(): string
    {
        $engine = $this->laravel['query_builder'];

        return $engine->config('query_adapter.builder.directive.namespace') ?: $engine->config('query_adapter.builder.directive.path', 'Engine/Directives');
    }

    public function getFileName(): string
    {
        return Str::studly($this->argument('model').config('query_adapter.paths.directive_prefix'));
    }

    protected function getStubName(): string
    {
        return '/directives/engine-directive.stub';
    }
}
