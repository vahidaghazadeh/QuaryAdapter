<?php

namespace Opsource\QueryAdapter;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

class FacadesServiceProvider extends ServiceProvider
{
    public static function getDirectives(): array|\Illuminate\Support\Collection
    {
        $directory = config('query_adapter.directives');
        if ($directory) {
            return collect($directory);
        }
        return [];
    }

    public function register(): void
    {
        //        $this->app->bind(SearchableDirectiveInterface::class, function ($app) {
        //        });
        $directory = config('query_adapter.facades');
        if ($directory) {
            $this->scanAndBindFacades($directory);
        }
    }

    public function scanAndBindFacades(array $directory): void
    {
        foreach ($directory as $file) {
            $fcade = app($file);
            if (is_subclass_of($fcade::class, Facade::class)) {
                $this->app->bind($fcade::getFacadeAccessor(), function () use ($fcade) {
                    return new $fcade();
                });
            }
        }
    }

}
