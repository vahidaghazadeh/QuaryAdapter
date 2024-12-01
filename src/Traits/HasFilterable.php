<?php

namespace packages\Opsource\QueryAdapter\src\Traits;
use Illuminate\Http\Request;
use Opsource\QueryAdapter\Contracts\QueryAdapterFacade;

trait HasFilterable
{
    protected $request;
    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(QueryAdapterFacade $builder)
    {
        $this->builder = $builder;
        foreach ($this->filters() as $name => $value) {
            if ( ! method_exists($builder, $name)) {
                continue;
            }
            if (strlen($value)) {
                $this->$name($value);
            } else {
                $this->$name();
            }
        }

        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }
}
