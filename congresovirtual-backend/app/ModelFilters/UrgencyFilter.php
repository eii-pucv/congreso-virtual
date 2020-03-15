<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class UrgencyFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function proposal($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('urgencies.proposal_id', '=', $value);
        });
    }

    public function user($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('urgencies.user_id', '=', $value);
        });
    }

    public function proposalIsPublic($value)
    {
        return $this
            ->select('urgencies.*')
            ->join('proposals', 'urgencies.proposal_id', '=', 'proposals.id')
            ->where(function($query) use ($value) {
                return $query->where('proposals.is_public', '=', $value);
            });
    }
}
