<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class TaxonomyFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function value($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('taxonomies.value', 'LIKE', "%$value%");
        });
    }

    public function query($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('taxonomies.value', 'LIKE', "%$value%");
        });
    }
}
