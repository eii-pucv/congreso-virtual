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

    public function project($name) {
        return $this
            ->where('articles.project_id', '=', $name);
    }

    public function query($name)
    {
        return $this
            ->where('articles.titulo', 'LIKE', "%$name%")
            ->orWhere('articles.detalle', 'LIKE', "%$name%");
    }

    public function projectIsPublic($name)
    {
        return $this
            ->select('articles.*')
            ->join('projects', 'articles.project_id', '=', 'projects.id')
            ->where('projects.is_public', '=', $name);
    }
}
