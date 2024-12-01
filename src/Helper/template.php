<?php

namespace Opsource\QueryAdapter\Helper;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Engine\Jobs\EngineUpdateJob;
use Opsource\QueryAdapter\Contracts\BoolQuery;
use Opsource\QueryAdapter\Facade\QueryAdapterFacade;
use Opsource\QueryAdapter\Filters\BoolQueryBuilder;
use Opsource\QueryAdapter\Performers\MatchOptions;
use Opsource\QueryAdapter\Tools\SortOrder;

class template
{
    protected QueryAdapterFacade $adapter;
    protected QueryAdapterFacade $query;

    public function queries()
    {
        return$this->adapter->query()
            ->where('status', '=', 1)
            ->where('is_in_stock', '=', 1)
            ->where('price', '>', 0)
            ->where('special_price', '>', 0)
            ->where('qty', '>', 0)
            ->where('special_to_date', '>', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('special_from_date', '>=', Carbon::now()->format('Y-m-d H:i:s'))
            ->get()
            ->select(['name', 'en_name', 'price', 'special_price', 'special_from_date', 'special_to_date', 'attributes'])
            ->sortBy('id', SortOrder::DESC);
    }

    public function moreLike()
    {
        $query = $this->getDdapter()->query();
        $query->moreLike(['en_name', 'name'], 'search', [
            "min_term_freq" => 1,
            "min_doc_freq" => 1,
            "analyzer" => "standard"
        ]);
    }

    public function analyzer()
    {
        $text = "text";
        $params = [
            'index' => 'index name',
            'body' => [
                'analyzer' => 'targets_analyzer',
                'text' => $text
            ]
        ];
        return $this->getDdapter()->indicesAnalyzers($params);
    }

    public function getDdapter()
    {
        return QueryAdapterFacade::adapter();
    }

    public function addMustBool()
    {
        return $this->query->addMustBool(function (BoolQuery $query) {
            $query->like(['name', 'en_name'], 'text', 'and');
        });
    }

    public function addMustBoolSelecet($attribute)
    {
        return $this->adapter->query()->addMustBool(function (BoolQueryBuilder $query) use ($attribute) {
            $query->where('filter_attributes.id', $attribute->attribute_id)
                ->where('filter_attributes.value', $attribute->value);
        })
            ->get()
            ->select(['id', 'name', 'en_name', 'status', 'is_in_stock'])
            ->sortBy('is_in_stock', SortOrder::DESC);


    }

    public function nested()
    {
        return $this->query->nested('filter_attributes', function (BoolQueryBuilder $builder) {
            return $builder->addMustBool(function (BoolQueryBuilder $builder) {
                $builder
                    ->whereIn('filter_attributes.type', ['select', 'multiselect'])
                    ->whereMatch('filter_attributes.id', 91, MatchOptions::make('and'))
                    ->whereMatch('filter_attributes.value.keyword', 'دارد', MatchOptions::make('and'));
            });
        });
    }

    public function runWithJob(): void
    {
        $model = Model::class;
        $searchEngine = $model->searchEngine()
            ->adapter()
            /**
             * @params => job class [JobClass::class]
             * @params (optional) => Closure
             * */
            ->setJob(EngineUpdateJob::class)
            ->setJob(EngineUpdateJob::class, fn () => "closure")
            /**
             * Broker args, if using a Message Brokers
             * */
            ->setBrokerArgs(['queue' => "targets_pr_3", 'message' => "ok"])
            /**
             * target data, if the setEngineData parameter is empty, the default information of the model itself will be taken into account
             * The name of a model method can be placed as an argument
             * */
            ->setQueueArgs(['args' => "value", 'args1' => "value"])
            /**
             * target data, if the setEngineData parameter is empty, the default information of the model itself will be taken into account
             * The name of a model method can be placed as an argument
             * */
            ->setEngineData()
            ->runWithJob(false);
    }
}
