<?php

namespace Opsource\QueryAdapter\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeEngineCommand extends Command
{
    protected $signature = 'engine:make {type} {--model=?} {--module=?} {--index=?} {--force} {--facade} {--job}';

    protected $description = 'Run query adapter commands';

    public function handle()
    {
        $type = $this->argument('type');
        switch ($type) {
            case 'directive':
                $this->call('engine:make-directive', $this->getArguments());
            case 'facade':
                return $this->makeFacade();
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
