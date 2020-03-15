<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ProjectFilter extends ModelFilter
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
            return $query->where('projects.titulo', 'LIKE', "%$value%");
        });
    }

    public function postulante($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('projects.postulante', 'LIKE', "%$value%");
        });
    }

    public function detalle($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('projects.detalle', 'LIKE', "%$value%");
        });
    }

    public function resumen($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('projects.resumen', 'LIKE', "%$value%");
        });
    }

    public function boletin($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('projects.boletin', '=', $value);
        });
    }

    public function estado($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('projects.estado', '=', $value);
        });
    }

    public function etapa($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('projects.etapa', '=', $value);
        });
    }

    public function isPrincipal($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('projects.is_principal', '=', $value);
        });
    }

    public function isEnabled($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('projects.is_enabled', '=', $value);
        });
    }

    public function isPublic($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('projects.is_public', '=', $value);
        });
    }

    public function query($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('projects.titulo', 'LIKE', "%$value%")
                ->orWhere('projects.postulante', 'LIKE', "%$value%")
                ->orWhere('projects.detalle', 'LIKE', "%$value%")
                ->orWhere('projects.resumen', 'LIKE', "%$value%");
            });
    }

    public function terms($value)
    {
        return $this
            ->join('project_term', 'projects.id', '=', 'project_term.project_id')
            ->select('projects.*')
            ->where(function($query) use ($value) {
                return $query->whereIn('project_term.term_id', $value);
            })
            ->distinct();
    }
}
