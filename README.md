Here’s an optimized version of your document:

---

# QueryAdapter

QueryAdapter is a powerful Laravel package that provides an intuitive abstraction layer for interacting with Elasticsearch indices. It includes a set of query builders designed for searching, aggregating, and suggesting data, while offering essential utilities for efficient index management. This package enables developers to work with Elasticsearch in a structured and efficient way, eliminating the complexity of low-level queries.

## Installation

To get started, install the package via Composer:

```sh
composer require opsource/query-adapter
```

## Usage

### Setting Up

Before using the QueryAdapter package, ensure that your Elasticsearch client is properly configured. The package relies on `ElasticClient` to manage all Elasticsearch communications, ensuring smooth data indexing and retrieval.

### Interacting with Indices

The `InteractsWithIndex` trait offers powerful methods to manage and query Elasticsearch indices efficiently.

#### Bulk Insert

Insert multiple documents into an Elasticsearch index in a single operation for improved performance:

```php
$data = [
    ['index' => ['_id' => 1]],
    ['name' => 'Product A', 'price' => 100],
    ['index' => ['_id' => 2]],
    ['name' => 'Product B', 'price' => 200],
];

$result = $this->bulk($data);
```

#### Fetch Index Information

Retrieve detailed information about a specific index, including settings and mappings:

```php
$indexInfo = $this->catIndices('my_index');
```

#### Delete an Index

Remove an index when it is no longer needed:

```php
$result = $this->indicesDelete('my_index');
```

#### Create a New Index with Custom Settings

Define and create a new Elasticsearch index with custom settings:

```php
$settings = [
    'settings' => [
        'number_of_shards' => 1,
        'number_of_replicas' => 1
    ]
];

$result = $this->indicesIndex('my_new_index', $settings);
```

#### Refresh an Index

Make recent operations visible to search queries:

```php
$result = $this->indicesRefresh();
```

### Querying Data

QueryAdapter simplifies querying with its builder-based approach.

#### Search Query

Perform a basic search query:

```php
$query = $this->query()->match('name', 'Product A')->get();
```

#### Index Class Example

Create an index class similar to an Eloquent model:

```php
use Ensi\LaravelElasticQuery\ElasticIndex;

class ProductsIndex extends ElasticIndex
{
    protected string $name = 'test_products';
    protected string $indicator = 'product_id';
}
```

Set a unique document attribute name for `$indicator`, which is used as an additional sort in `search_after`.

#### Query Example

Perform a search with complex filters and sorting:

```php
$searchQuery = ProductsIndex::queryEngine();

$hits = $searchQuery
             ->where('rating', '>=', 5)
             ->whereDoesntHave('offers', fn(BoolQuery $queryEngine) => $queryEngine->where('seller_id', 10)->where('active', false))
             ->sortBy('rating', 'desc')
             ->sortByNested('offers', fn(SortableQuery $queryEngine) => $queryEngine->where('active', true)->sortBy('price', mode: 'min'))
             ->take(25)
             ->get();
```

### Filtering

```php
$searchQuery->where('field', 'value');
$searchQuery->where('field', '>', 'value'); // Operators: `=`, `!=`, `>`, `<`, `>=`, `<=`
$searchQuery->whereNot('field', 'value'); // Equivalent to `where('field', '!=', 'value')`
$searchQuery->whereIn('field', ['value1', 'value2']);
$searchQuery->whereNotIn('field', ['value1', 'value2']);
$searchQuery->whereNull('field');
$searchQuery->whereNotNull('field');
```

### Nested Queries

```php
$searchQuery->whereHas('nested_field', fn(BoolQuery $subQuery) => $subQuery->where('field_in_nested', 'value'));
$searchQuery->whereDoesntHave('nested_field', function (BoolQuery $subQuery) {
    $subQuery->whereHas('nested_field', fn(BoolQuery $subQuery2) => $subQuery2->whereNot('field', 'value'));
});
```

`nested_field` must have `nested` type. Subqueries can only use subdocument fields.

### Full-Text Search

```php
$searchQuery->whereMatch('field_one', 'queryEngine string');
$searchQuery->whereMultiMatch(['field_one^3', 'field_two'], 'queryEngine string', MatchType::MOST_FIELDS);
```

### Sorting

```php
$searchQuery->sortBy('field', SortOrder::DESC, SortMode::MAX, MissingValuesMode::FIRST);
$searchQuery->sortByNested('nested_field', fn(SortableQuery $subQuery) => $subQuery->where('field_in_nested', 'value')->sortBy('field'));
```

Use dedicated sort methods for each sort type:

```php
$searchQuery->minSortBy('field', 'asc');
$searchQuery->maxSortBy('field', 'asc');
$searchQuery->avgSortBy('field', 'asc');
$searchQuery->sumSortBy('field', 'asc');
$searchQuery->medianSortBy('field', 'asc');
```

### Pagination

#### Offset Pagination

```php
$page = $searchQuery->paginate(15, 45);
```

#### Cursor Pagination

```php
$page = $searchQuery->cursorPaginate(10);
$pageNext = $searchQuery->cursorPaginate(10, $page->next);
```

### Aggregation

Create aggregation queries:

```php
$aggQuery = ProductsIndex::aggregate();

$aggs = $aggQuery
            ->where('active', true)
            ->terms('codes', 'code')
            ->count('product_count', 'product_id')
            ->nested(
                'offers',
                fn(AggregationsBuilder $builder) => $builder->where('seller_id', 10)->minmax('price', 'price')
            );
```

#### Aggregate Types

```php
$aggQuery->terms('agg_name', 'field', 25);
$aggQuery->minmax('agg_name', 'field');
$aggQuery->count('agg_name', 'field');
```

### Suggesting

Create suggest queries for autocomplete or typo correction:

```php
$sugQuery = ProductsIndex::suggest();
$suggests = $sugQuery->phrase('suggestName', 'name.trigram')->text('glves')->size(1)->shardSize(3)->get();
```

### Suggester Types

Term Suggester:

```php
$aggQuery->term('suggestName', 'name.trigram')->text('glves')->get();
```

Phrase Suggester:

```php
$aggQuery->phrase('suggestName', 'name.trigram')->text('glves')->get();
```

## CLI Commands

### `engine:make`

Generates various engine components:

```sh
php artisan engine:make {type} [--model=] [--module=] [--index=] [--force] [--facade] [--job]
```

### `engine:make-facade`

Generates a facade for a search engine model.

### `engine:make-directive`

Creates a directive class for a search engine model.

## Query Log

Enable query logging to track executed queries:

```php
ElasticQuery::enableQueryLog();
$records = ElasticQuery::getQueryLog();
ElasticQuery::disableQueryLog();
```

## Environment Variables

Configure the following environment variables:

```dotenv
ELASTICSEARCH_HOSTS=https://localhost:9200
ELASTICSEARCH_RETRIES=2
ELASTICSEARCH_USERNAME=admin
ELASTICSEARCH_PASSWORD=admin
ELASTICSEARCH_SSL_VERIFICATION=true
```

## Elasticsearch Version Compatibility

Separate releases are created for Elasticsearch 7 and 8. Development for each version occurs in corresponding branches.

## Contributing

See [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

### Testing

1. `composer install`
2. `npm i`
3. Start Elasticsearch
4. Copy `phpunit.xml.dist` to `phpunit.xml` and set correct environment variables
5. `composer test`

## License

MIT License. See [LICENSE.md](LICENSE.md) for more information.

---

This version improves readability and structure while maintaining clarity. It consolidates sections, removes redundancy, and ensures consistency throughout the document.