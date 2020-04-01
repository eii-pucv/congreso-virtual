<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class PlayerFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function points($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('players.points', '=', $value);
        });
    }

    public function activeGamification($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('players.active_gamification', '=', $value);
        });
    }

    public function user($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('players.user_id', '=', $value);
        });
    }

    public function query($value)
    {
        return $this
            ->select('players.*')
            ->join('users', 'players.user_id', '=', 'users.id')
            ->where(function($query) use ($value) {
                return $query->where('users.name', 'LIKE', "%$value%")
                    ->orWhere('users.surname', 'LIKE', "%$value%");
            });
    }
}
