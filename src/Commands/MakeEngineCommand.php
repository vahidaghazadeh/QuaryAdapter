<?php

namespace Opsource\QueryAdapter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeEngineCommand extends Command
{
    protected $signature = 'engine:make {type} {--model=?} {--module=?} {--index=?} {--force} {--facade} {--job}';

    protected $description = 'Run query adapter commands';

    public function handle()
    {
        $type = $this->argument('type');
//        $this->info("OK:". $type);
//        Log::debug(\json_encode($this->getOptions()));
//        Log::debug(\json_encode($this->parseOptions()));
//        $this->info(\json_encode($this->parseOptions()));
//        return 0;
        switch ($type) {
            case 'directive':
                $this->call('engine:make-directive', $this->getArguments());
                // no break
            case 'facade':
                return $this->makeFacade();
                // Add cases for other types if needed
            default:
                $this->error("Invalid type '{$type}'. Available types are: facade");
                return 1; // Return error code
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['type', InputArgument::REQUIRED, 'An existence that will be created'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
//    protected function getOptions()
//    {
//        return [
//            ['model',false, InputOption::VALUE_OPTIONAL, 'The model to use.'],
//            ['module', false,InputOption::VALUE_OPTIONAL, 'The model to use.'],
//            ['force', false,InputOption::VALUE_OPTIONAL, 'Force the operation to run when in production.'],
//            ['facade', false,InputOption::VALUE_OPTIONAL, 'Make facade.'],
//            ['job', false,InputOption::VALUE_OPTIONAL, 'Make job'],
//        ];
//    }

    protected function parseOptions()
    {
        $parsedOptions = [];

        foreach ($this->getOptions() as $option) {
            $optionName = $option[0];
            $optionValue = $this->option($optionName);

            // Include only options with non-null values
            if ($optionValue) {
                $parsedOptions[$optionName] = $optionValue;
            }
        }

        return $parsedOptions;
    }
}
