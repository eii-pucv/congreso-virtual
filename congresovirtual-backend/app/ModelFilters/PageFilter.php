<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class PageFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function query($name)
    {
        return $this
            ->where('title', 'LIKE', "%$name%")
            ->orWhere('slug', 'LIKE', "%$name%")
            ->orWhere('content', 'LIKE', "%$name%");
    }
}
