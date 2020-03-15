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

    public function titulo($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('proposals.titulo', 'LIKE', "%$value%");
        });
    }

    public function detalle($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('proposals.detalle', 'LIKE', "%$value%");
        });
    }

    public function autoria($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('proposals.autoria', 'LIKE', "%$value%");
        });
    }

    public function boletin($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('proposals.boletin', '=', $value);
        });
    }

    public function argument($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('proposals.argument', 'LIKE', "%$value%");
        });
    }

    public function state($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('proposals.state', '=', $value);
        });
    }

    public function type($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('proposals.type', '=', $value);
        });
    }

    public function user($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('proposals.user_id', '=', $value);
        });
    }

    public function isPublic($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('proposals.is_public', '=', $value);
        });
    }

    public function query($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('proposals.titulo', 'LIKE', "%$value%")
                ->orWhere('proposals.detalle', 'LIKE', "%$value%")
                ->orWhere('proposals.autoria', 'LIKE', "%$value%")
                ->orWhere('proposals.boletin', 'LIKE', "%$value%")
                ->orWhere('proposals.argument', 'LIKE', "%$value%");
        });
    }
}
