<?php

namespace Opsource\QueryAdapter\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Opsource\QueryAdapter\Contracts\SearchableDirectiveInterface;

trait HasUseSearchEngine
{
    //    protected SearchEngine $engine;

    public bool $preventObserver = true;

    public static function bootHasUseSearchEngine(): void
    {

        static::created(function ($model) {
            Log::debug("created");
            Log::debug("preventObserver updated: " . json_encode($model->searchEngine()->adapter()->getEngineData()));
            $model->searchEngine()
                ->setPreventObserver($model->preventObserver)
                ->adapter()
                ->setEngineData()
                ->createDoc();
        });

        static::updated(function (Model $model) {

            Log::debug("preventObserver updated: " . json_encode($model));
            Log::debug("preventObserver updated: " . json_encode($model->preventObserver));
            $model->searchEngine()
                ->setPreventObserver($model->preventObserver)
                ->adapter()
                ->setEngineData()
                ->updateDoc();
        });

        static::deleted(function ($model) {
            Log::debug("deleted");
            $model->searchEngine()
                ->setPreventObserver($model->preventObserver)
                ->adapter()
                ->deleteDoc($model->id);
        });
    }


    //    /**
    //     * @throws QueryAdapterException
    //     * @throws ClientResponseException
    //     * @throws ServerResponseException
    //     * @throws MissingParameterException
    //     * @throws BindingResolutionException
    //     */
    //    public static function boot(): void
    //    {
    //        parent::boot();
    //        self::created(
    //            function (Model $model) {
    //                if($model->preventObserver) {
    //                    $engine = $model->searchEngine();
    //                    Assert::allNotEmpty($engine->getEngineData()) ?? $engine->setEngineData();
    //                    $engine->addToIndex([
    //                        'index' => $engine->getIndexName(),
    //                        'body' => $engine->getEngineData()
    //                    ]);
    //                }
    //            }
    //        );
    //
    //        static::updated(
    //            function (Model $model) {
    //                if($model->preventObserver) {
    //                    $engine = $model->searchEngine();
    //                    Assert::allNotEmpty($engine->getEngineData()) ?? $engine->setEngineData();
    //                    $engine->addToUpdate([
    //                        'index' => $engine->getIndexName(),
    //                        'id' => $model->id,
    //                        'body' => [
    //                            'doc' => $engine->getEngineData(),
    //                        ],
    //                    ]);
    //                }
    //                //                $engine->enableLog();
    //            }
    //        );
    //
    //        static::deleted(function (Model $model) {
    //            if($model->preventObserver) {
    //                Log::debug("Start delete {$model->id}");
    //                $model->searchEngine()->documentDelete($model->id);
    //            }
    //        });
    //    }

    /**
     * @return SearchableDirectiveInterface
     * | Any entity that uses this method is required to override the searchEngine
     * | Before rewriting, you must create the directive of the entity and call the directive
     * | and pass $this to its instance so that you can use the features of the QueryAdapter
     * | package during CRUD of the entity.
     * | Example: EntityEngineDirective::instance($this)
     */
    abstract public function searchEngine(): SearchableDirectiveInterface;
}
