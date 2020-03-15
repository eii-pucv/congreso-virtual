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

    public function titulo($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('public_consultations.titulo', 'LIKE', "%$value%");
        });
    }

    public function autor($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('public_consultations.autor', 'LIKE', "%$value%");
        });
    }

    public function detalle($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('public_consultations.detalle', 'LIKE', "%$value%");
        });
    }

    public function resumen($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('public_consultations.resumen', 'LIKE', "%$value%");
        });
    }

    public function estado($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('public_consultations.estado', '=', $value);
        });
    }

    public function query($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('public_consultations.titulo', 'LIKE', "%$value%")
                ->orWhere('public_consultations.autor', 'LIKE', "%$value%")
                ->orWhere('public_consultations.detalle', 'LIKE', "%$value%")
                ->orWhere('public_consultations.resumen', 'LIKE', "%$value%");
        });
    }

    public function terms($value)
    {
        return $this
            ->join('public_consultation_term', 'public_consultations.id', '=', 'public_consultation_term.public_consultation_id')
            ->select('public_consultations.*')
            ->where(function($query) use ($value) {
                return $query->whereIn('public_consultation_term.term_id', $value);
            })
            ->distinct();
    }
}
