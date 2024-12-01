<?php

namespace Opsource\QueryAdapter\Jobs;

use Closure;
use Illuminate\Foundation\Bus\PendingClosureDispatch;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Facades\Log;
use Opsource\QueryAdapter\Contracts\AdapterIfc;

class JobBuilder
{
    protected AdapterIfc $adapter;
    public function __construct(AdapterIfc $adapter)
    {
        $this->adapter = $adapter;
    }

    public function fire(Closure $closure = null): mixed
    {
        $jobClass = $this->adapter->getJob();
        //        if(is_callable($closure)) {
        //            new PendingClosureDispatch(
        //                CallQueuedClosure::create(
        //                    $this->adapter->getQueueArgs()->search('closure')->first()
        //                )
        //            );
        //            $jobClass::dispatch($this->adapter);
        //            return "start job with closure";
        //        }

        $jobClass::dispatch($this->adapter);
        Log::debug("start job {$jobClass}");
        return "start job";
    }

    //    protected function dispatchJob()
    //    {
    //        $jobClass = $this->adapter->getJob();
    //        if($this->adapter->getQueueArgs()->search('execute_type')->first() === "closure") {
    //            $this->adapter->setQueueArgs(['execute_type' => "default"]);
    //            $cl = $this->adapter->getJob();
    //            $cl->call($this->adapter);
    //            new PendingClosureDispatch(
    //                CallQueuedClosure::create(
    //                    $this->adapter->getQueueArgs()->search('closure')->first()
    //                ));
    //            app()->bindMethod($this->adapter->getJob().'@handle', function ($job, $app) {
    //                return $job->handle($app->make(AudioProcessor::class));
    //            });
    //        } else {
    //        $jobClass::dispatch($this->adapter);
    //        }
    //        $jobClass = $this->adapter->getJob();
    //        \Log::debug($jobClass);
    //        dispatch($jobClass::dispatch($this->adapter, $this->adapter->getQueueArgs()));
    //        return "dispached";
    //    }
    //
    //    /**
    //     * @return void
    //     */
    //    public function executeWitchClosure(): void
    //    {
    //        new PendingClosureDispatch(CallQueuedClosure::create($this->adapter->getJob()));
    //    }
    //
    //    /**
    //     * @return void
    //     */
    //    public function executeWitchMessageBroker(): void
    //    {
    //        new PendingClosureDispatch(CallQueuedClosure::create($this->adapter->getJob()));
    //    }
}
