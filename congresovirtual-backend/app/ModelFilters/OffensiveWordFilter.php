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

    public function word($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('offensive_words.word', 'LIKE', "%$value%");
        });
    }

    public function query($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('offensive_words.word', 'LIKE', "%$value%");
        });
    }
}
