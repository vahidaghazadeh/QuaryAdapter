<?php

namespace Opsource\QueryAdapter\Exceptions;

use Exception;
use Opsource\QueryAdapter\Traits\Response;

class QueryAdapterException extends Exception implements QueryAdapterExceptionIfc
{
    use Response;
}
