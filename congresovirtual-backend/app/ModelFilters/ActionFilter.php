<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ActionFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function type($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('actions.type', '=', $value);
        });
    }

    public function subtype($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('actions.subtype', '=', $value);
        });
    }

    public function points($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('actions.points', '=', $value);
        });
    }

    public function query($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('actions.type', 'LIKE', "%$value%")
                ->orWhere('actions.subtype', 'LIKE', "%$value%");
        });
    }
}
