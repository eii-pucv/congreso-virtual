<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ProposalFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function titulo($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('titulo', 'LIKE', "%$name%");
        });
    }

    public function detalle($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('detalle', 'LIKE', "%$name%");
        });
    }

    public function autoria($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('autoria', 'LIKE', "%$name%");
        });
    }

    public function boletin($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('boletin', 'LIKE', "%$name%");
        });
    }

    public function query($name)
    {
        return $this
            ->where('titulo', 'LIKE', "%$name%")
            ->orWhere('detalle', 'LIKE', "%$name%")
            ->orWhere('autoria', 'LIKE', "%$name%")
            ->orWhere('boletin', 'LIKE', "%$name%");
    }
}
