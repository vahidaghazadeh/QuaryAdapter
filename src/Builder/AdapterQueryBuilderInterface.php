<?php

namespace Opsource\QueryAdapter\Builder;

interface AdapterQueryBuilderInterface
{
    public function getKeyName();

    public function setKeyName(string $keyName);

    public function isMappingProperties();

    public function setMappingProperties(bool $mappingProperties);

    public function getOrder();

    public function setOrder(array $order);

    public function getLimit();

    public function setLimit(int $limit);

    public function getOffset();

    public function setOffset(int $offset);

    public function getPage();

    public function setPage(int $page);

    public function getEsHosts();

    public function setEsHosts($esHosts);

    public function getEsClient();

    public function setEsClient($esClient);

    public function getRecordsPerPage();

    public function setRecordsPerPage($recordsPerPage);

    public function getRawResults();

    public function setRawResults($rawResults);

    public function getNestedQueries();

    public function setNestedQueries(array $nestedQueries);

    public function getAggs();

    public function setAggs(array $aggs);

    public function getWith();

    public function setWith(array $with);

    public function getWithOut();

    public function setWithOut(array $withOut);

    public function isPrependedPath();

    public function setPrependedPath(bool $prependedPath);

    public function getModel();

    public function getQuery();

    public function setQuery($query);

    public function getBody();

    public function setBody($body);

    public function getMinScore();

    public function setMinScore($minScore);

    public function isScrollAlive();

    public function setScrollAlive(bool $scrollAlive);

    public function isScrollSize();

    public function setScrollSize(bool $scroll_size);

    public function getIndexName();

    public function setIndexName(string $indexName);

    public function getTypeName();

    public function setTypeName(string $typeName);

    public function getValidation();

    public function setValidation(string $validation);
}
