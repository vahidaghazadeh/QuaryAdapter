<?php

namespace Opsource\QueryAdapter\Helper;

class ClosureContext
{
    /**
     * @var ClosureScope Closures scope
     */
    public $scope;

    /**
     * @var int
     */
    public $locks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->scope = new ClosureScope();
        $this->locks = 0;
    }
}
