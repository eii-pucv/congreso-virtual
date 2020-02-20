<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class TermFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function value($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('value', 'LIKE', "%$name%");
        });
    }

    public function query($name)
    {
        return $this
            ->where('value', 'LIKE', "%$name%");
    }
}
