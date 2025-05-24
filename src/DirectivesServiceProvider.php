<?php

namespace Opsource\QueryAdapter;

use Illuminate\Support\ServiceProvider;
use Opsource\QueryAdapter\Contracts\SearchIndex;
use Opsource\QueryAdapter\Performers\EngineDirectiveAdapter;

class DirectivesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EngineDirectiveAdapter::class, EngineDirectiveAdapter::class);
        //        $this->app->bind(SearchableDirectiveInterface::class, function ($app) {
        //        });
        //        $directory = config('query_adapter.directives');
        //        if($directory) {
        //            $this->scanAndBindDirectives($directory);
        //        }
    }


    public function scanAndBindDirectives(array $directory): void
    {
        foreach ($directory as $file) {
            $directive = app($file);
            if (is_subclass_of($directive, SearchIndex::class)) {
                $this->app->bind($directive::class, function () use ($directive) {
                    return new $directive();
                });
                //                $parts = explode('\\', $file);
                //                $lastPart = end($parts);
                //                $facade = str_replace("Directive", "Facade", $lastPart);
                //                $this->app->bind($facade, function () use ($directive) {
                //                    return new $directive();
                //                });
            }
        }

    }

    public static function getDirectives(): array|\Illuminate\Support\Collection
    {
        $directory = config('query_adapter.directives');
        if($directory) {
            return collect($directory);
        }
        return [];
    }

}
