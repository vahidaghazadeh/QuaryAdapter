<?php

namespace Opsource\QueryAdapter\Contracts;

interface SearchIndex
{
    /**
     * Returns the name of attribute with unique values in index scope.
     *
     * @return string
     */
    public function indicator(): string;
    /**
     * Perform search query.
     *
     * @param array $dsl
     * @return array
     */
    public function search(array $dsl): array;

    /**
     * Perform delete by query.
     *
     * @param array $dsl
     * @return array
     */
    public function deleteByQuery(array $dsl): array;

}
