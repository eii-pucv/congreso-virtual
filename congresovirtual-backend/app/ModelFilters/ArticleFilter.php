<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ArticleFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function project($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('articles.project_id', '=', $value);
        });
    }

    public function query($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('articles.titulo', 'LIKE', "%$value%")
                ->orWhere('articles.detalle', 'LIKE', "%$value%");
        });
    }

    public function projectIsPublic($value)
    {
        return $this
            ->select('articles.*')
            ->join('projects', 'articles.project_id', '=', 'projects.id')
            ->where(function($query) use ($value) {
                return $query->where('projects.is_public', '=', $value);
            });
    }
}
