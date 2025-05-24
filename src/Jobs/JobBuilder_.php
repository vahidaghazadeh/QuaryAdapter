<?php
//
//namespace packages\Opsource\QueryAdapter\src\Jobs;
//
//use App\Performers\RabbitMQDirective;
//use Illuminate\Contracts\Queue\ShouldBeUnique;
//use Opsource\QueryAdapter\Exceptions\QueryAdapterException;
//use Opsource\QueryAdapter\Jobs\JobManager;
//use Opsource\QueryAdapter\Performers\SearchEngineJobBuilder;
//use Opsource\QueryAdapter\Traits\JobDecorator;
//
//class JobBuilder
//{
//    use JobDecorator;
//
//    public function __construct(protected SearchEngineJobBuilder $builder)
//    {
//    }
//    public function makeJob(...$arguments): JobDecorator
//    {
//        if ($this->jobShouldBeBroker()) {
//            return static::makeUniqueJob(...$arguments);
//        }
//
//        return new JobManager::$jobDecorator(static::class, ...$arguments);
//    }
//
////    protected function makeUniqueJob(...$arguments): JobDecorator
////    {
////        if($this->builder->getBroker()){
////            return new JobManager::$jobDecorator(static::class, ...$arguments);
////        }
////    }
////
////    public static function jobShouldBeBroker(): bool
////    {
////
////    }
////
////    public static function jobShouldBeBroker(): bool
////    {
////        return is_subclass_of(static::class, ShouldBeUnique::class);
////        return is_subclass_of(static::class, ShouldBeUnique::class);
////    }
////
////    public static function setJobDecorator(...$arguments)
////    {
////
////    }
////
////    protected function builder()
////    {
////        if (!$jobRunner->hasUseCurrentModel() && !$this->model() && !is_null($jobRunner->getDataBy())) {
////            throw new QueryAdapterException("Can't use null parameters");
////        }
////
////        if(!$this->getMethod($jobRunner->getJob(), 'withBroker')) {
////            throw new QueryAdapterException("The withBroker method does not exist in the {$jobRunner->getJob()} class");
////        }
////
////        $construct = $this->getMethod($jobRunner->getJob(), '__construct');
////        if ($construct->isPublic() && $construct->getNumberOfParameters()) {
////            foreach ($construct->getParameters() as $parameter) {
////                if(!$parameter->isVariadic() && is_subclass_of($this->model, $parameter->getType()->getName())) {
////                    $jobRunner->pushParameter(["__construct" => $this->model()]);
////                }
////            }
////        }
////
////        $broker = $this->getMethod($jobRunner->getJob(), 'withBroker');
////        if ($broker->isPublic() && $broker->getNumberOfParameters()) {
////            foreach ($broker->getParameters() as $parameter) {
////                if(!$parameter->isVariadic() && is_subclass_of(RabbitMQDirective::class, $parameter->getType()->getName())) {
////                    $jobRunner->pushParameter(["__broker" => $jobRunner->getBroker()]);
////                }
////            }
////        }
////
////
////        if ($jobRunner->hasUseCurrentModel() && $this->model() && !is_null($jobRunner->getDataBy())) {
////            $jobRunner->pushParameter($this->setEngineData()->getEngineData());
////        }
////
////        if (!$jobRunner->hasUseCurrentModel() && $this->model() && !is_null($jobRunner->getDataBy())) {
////            $jobRunner->pushParameter($this->setEngineData($jobRunner->getDataBy())->getEngineData());
////        }
////        return $jobRunner;
////    }
//}
