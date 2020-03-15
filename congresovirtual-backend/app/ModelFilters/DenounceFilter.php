<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class DenounceFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function comment($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('denounces.comment_id', '=', $value);
        });
    }

    public function user($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('denounces.user_id', '=', $value);
        });
    }

    public function query($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('denounces.reason', 'LIKE', "%$value%")
                ->orWhere('denounces.description', 'LIKE', "%$value%");
        });
    }
}
