<?php

namespace Opsource\QueryAdapter\Performers;

class SearchEngineJobBuilder extends SearchEngineJobBuilderAbs
{
    public function dispatch()
    {
        if ($_cons = $this->array_recursive_search_key_map('__construct', $this->parameters->get())) {
            $this->job::dispatch($_cons)->onQueue($this->queue);
            return true;
        }

        if ($_cons = $this->array_recursive_search_key_map('__broker', $this->parameters->get())) {
            $this->job::dispatch($_cons)->withBroker($this->getBroker())->onQueue($this->queue);
            return true;
        }

        return true;
    }

    public function array_recursive_search_key_map($needle, $haystack)
    {
        foreach ($haystack as $key => $value) {
            if ($key === $needle) {
                return $value;
            } elseif (is_array($value)) {
                $result = $this->array_recursive_search_key_map($needle, $value);
                if ($result !== false) {
                    return $result;
                }
            }
        }
        return false;
    }

}
