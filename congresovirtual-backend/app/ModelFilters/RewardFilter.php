<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class RewardFilter extends ModelFilter
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
            return $query->where('rewards.name', 'LIKE', "%$value%");
        });
    }

    public function points($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('rewards.points', '=', $value);
        });
    }

    public function actionsNeeded($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('rewards.actions_needes', '=', $value);
        });
    }

    public function action($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('rewards.action_id', '=', $value);
        });
    }

    public function actionType($value)
    {
        return $this
            ->select('rewards.*')
            ->join('actions AS actions_1', 'rewards.action_id', '=', 'actions_1.id')
            ->where(function($query) use ($value) {
                return $query->where('actions_1.type', '=', $value);
            });
    }

    public function actionSubtype($value)
    {
        return $this
            ->select('rewards.*')
            ->join('actions AS actions_2', 'rewards.action_id', '=', 'actions_2.id')
            ->where(function($query) use ($value) {
                return $query->where('actions_2.subtype', '=', $value);
            });
    }

    public function query($value)
    {
        return $this
            ->select('rewards.*')
            ->join('actions AS actions_3', 'rewards.action_id', '=', 'actions_3.id')
            ->where(function($query) use ($value) {
                return $query->where('rewards.name', 'LIKE', "%$value%")
                    ->orWhere('actions_3.type', 'LIKE', "%$value%")
                    ->orWhere('actions_3.subtype', 'LIKE', "%$value%");
            });
    }
}
