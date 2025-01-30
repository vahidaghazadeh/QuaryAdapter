<?php

namespace Opsource\QueryAdapter\Commands;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Module;
use Opsource\QueryAdapter\Support\BuilderConfigReader;
use Opsource\QueryAdapter\Support\GeneratorCommand;
use Opsource\QueryAdapter\Support\StubBuilder;
use Symfony\Component\Console\Input\InputArgument;

class EngineJobCommand extends GeneratorCommand implements QueryAdapterCommandBuilderIfc
{
    protected $argumentName = 'job';
    protected $signature = 'engine:make-job {name} {model?} {module?} {--broker}';

    protected $description = 'Make query adapter search engine job';

    public function getDestinationFilePath(): string
    {
        $jobPath = BuilderConfigReader::read('job');
        if ($this->argument('module')) {
            $path = Module::getModulePath($this->getModuleName());
            return "$path{$jobPath->getPath()}/{$this->getFileName()}.php";
        }
        return "{$jobPath->getDefault()}/{$this->getFileName()}.php";
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return Str::studly(ucfirst($this->argument('name')) . config('query_adapter.paths.job_prefix'));
    }

    public function getTemplateContents()
    {
        $this->hasModule = false;
        return (new StubBuilder($this->getStubName(), [
            'namespace' => $this->getDefaultNamespace(),
            'MODEL_NAMESPACE' => $this->getModelNameSpace(),
            'JOB_NAME' => $this->getFileName(),
            'CONSTRUCT' => $this->argument('model') ? 'public function __construct(protected Model $model){}' : '',
        ]))->render();
    }


    protected function getStubName(): string
    {
        if($this->option('broker') && $this->argument('model')) {
            return '/job/engine-job-with-broker.stub';
        }
        return '/job/engine-job.stub';
    }

    public function getDefaultNamespace(): string
    {
        if ($this->argument('module')) {
            return "{$this->getModuleNameSpace()}\\".str_replace(
                '/',
                '\\',
                Config::get('query_adapter.builder.job.path')
            );
        }
        return str_replace(
            '/',
            '\\',
            Config::has('query_adapter.builder.job.namespace') ?
                Config::get('query_adapter.builder.job.namespace') :
                Config::get('query_adapter.builder.job.path')
        );
    }

    public function getModelNameSpace()
    {
        if ($this->argument('model')) {
            if ($this->argument('module')) {
                return "use {$this->laravel['modules']->config('namespace')}\\{$this->getModuleName()}\\Models\\{$this->getModelName()};";
            } else {
                return "use App\\Models\\{$this->getModelName()};";
            }
        }
        return "";
    }

    public function getModelName()
    {
        return $this->argument('model') ?? '';
    }

    public function getIndicator(): string
    {
        return "";
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'Job name is mandatory.'],
            ['model', InputArgument::REQUIRED, 'Job name is mandatory.'],
        ];
    }
}
