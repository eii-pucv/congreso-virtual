<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class IdeaFilter extends ModelFilter
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
            ->where('ideas.project_id', '=', $name);
    }

    public function query($name)
    {
        return $this
            ->where('ideas.titulo', 'LIKE', "%$name%")
            ->orWhere('ideas.detalle', 'LIKE', "%$name%");
    }

    public function projectIsPublic($name)
    {
        return $this
            ->select('ideas.*')
            ->join('projects', 'ideas.project_id', '=', 'projects.id')
            ->where('projects.is_public', '=', $name);
    }
}
