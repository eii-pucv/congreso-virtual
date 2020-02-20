<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class PublicConsultationFilter extends ModelFilter
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

    public function autor($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('autor', 'LIKE', "%$name%");
        });
    }

    public function detalle($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('detalle', 'LIKE', "%$name%");
        });
    }

    public function resumen($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('resumen', 'LIKE', "%$name%");
        });
    }

    public function query($name)
    {
        return $this
            ->where('titulo', 'LIKE', "%$name%")
            ->orWhere('autor', 'LIKE', "%$name%")
            ->orWhere('detalle', 'LIKE', "%$name%")
            ->orWhere('resumen', 'LIKE', "%$name%");
    }

    public function terms($name)
    {
        return $this
            ->join('public_consultation_term', 'public_consultations.id', '=', 'public_consultation_term.public_consultation_id')
            ->select('public_consultations.*')
            ->whereIn('public_consultation_term.term_id', $name)
            ->distinct();
    }
}
