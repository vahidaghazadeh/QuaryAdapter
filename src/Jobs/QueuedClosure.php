<?php

namespace packages\Opsource\QueryAdapter\src\Jobs;

use Closure;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Opsource\QueryAdapter\Helper\SerializableClosure;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class QueuedClosure implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $closure;

    public function __construct(SerializableClosure $closure)
    {
        $this->closure = $closure;
    }


    public static function create(Closure $job): QueuedClosure
    {
        return new self(new SerializableClosure($job));
    }
}
