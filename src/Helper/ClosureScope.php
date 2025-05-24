<?php

namespace Opsource\QueryAdapter\Helper;

class ClosureScope extends \SplObjectStorage
{
    /**
     * @var int Number of serializations in current scope
     */
    public int $serializations = 0;

    /**
     * @var integer Number of closures that have to be serialized
     */
    public int $toserialize = 0;
}
