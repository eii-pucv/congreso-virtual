<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class OffensiveWordFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function word($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('word', 'LIKE', "%$name%");
        });
    }

    public function query($name)
    {
        return $this
            ->where('word', 'LIKE', "%$name%");
    }
}
