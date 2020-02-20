<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class UserFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function name($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('name', 'LIKE', "%$name%");
        });
    }

    public function surname($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('surname', 'LIKE', "%$name%");
        });
    }

    public function query($name)
    {
        return $this
            ->where('name', 'LIKE', "%$name%")
            ->orWhere('surname', 'LIKE', "%$name%");
    }

    public function terms($name)
    {
        return $this
            ->join('term_user', 'users.id', '=', 'term_user.user_id')
            ->select('users.*')
            ->whereIn('term_user.term_id', $name)
            ->distinct();
    }

    public function esExperto($name)
    {
        return $this
            ->join('user_metas AS user_metas_1', 'users.id', '=', 'user_metas_1.user_id')
            ->select('users.*')
            ->where([
                ['user_metas_1.key', 'es_experto'],
                ['user_metas_1.value', $name]
            ])
                ->distinct();
    }

    public function esOrganizacion($name)
    {
        return $this
            ->join('user_metas AS user_metas_2', 'users.id', '=', 'user_metas_2.user_id')
            ->select('users.*')
            ->where([
                ['user_metas_2.key', 'es_organizacion'],
                ['user_metas_2.value', $name]
            ])
            ->distinct();
    }
}
