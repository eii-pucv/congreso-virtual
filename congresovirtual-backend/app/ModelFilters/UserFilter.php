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

    public function name($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('users.name', 'LIKE', "%$value%");
        });
    }

    public function surname($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('users.surname', 'LIKE', "%$value%");
        });
    }

    public function username($value)
    {
        return $this
            ->join('user_metas AS user_metas_username', 'users.id', '=', 'user_metas_username.user_id')
            ->select('users.*')
            ->where(function($query) use ($value) {
                return $query->where([
                    ['user_metas_username.key', 'username'],
                    ['user_metas_username.value', $value]
                ]);
            })
            ->distinct();
    }

    public function query($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('users.name', 'LIKE', "%$value%")
                ->orWhere('users.surname', 'LIKE', "%$value%");
        });
    }

    public function terms($value)
    {
        return $this
            ->join('term_user', 'users.id', '=', 'term_user.user_id')
            ->select('users.*')
            ->where(function($query) use ($value) {
                return $query->whereIn('term_user.term_id', $value);
            })
            ->distinct();
    }

    public function esExperto($value)
    {
        return $this
            ->join('user_metas AS user_metas_1', 'users.id', '=', 'user_metas_1.user_id')
            ->select('users.*')
            ->where(function($query) use ($value) {
                return $query->where([
                    ['user_metas_1.key', 'es_experto'],
                    ['user_metas_1.value', $value]
                ]);
            })
            ->distinct();
    }

    public function esOrganizacion($value)
    {
        return $this
            ->join('user_metas AS user_metas_2', 'users.id', '=', 'user_metas_2.user_id')
            ->select('users.*')
            ->where(function($query) use ($value) {
                return $query->where([
                    ['user_metas_2.key', 'es_organizacion'],
                    ['user_metas_2.value', $value]
                ]);
            })
            ->distinct();
    }
}
