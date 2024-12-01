<?php

namespace Opsource\QueryAdapter;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class CommandsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands(config('query_adapter.commands', self::defaultCommands()->toArray()));
    }

    public function provides(): array
    {
        return self::defaultCommands()->toArray();
    }

    /**
     * @return Collection
     */
    public static function defaultCommands(): Collection
    {
        return collect([
            Commands\MakeEngineCommand::class,
            Commands\EngineJobCommand::class,
            Commands\EngineModelDirectorCommand::class,
            Commands\EngineFacadeCommand::class
        ]);
    }


}
