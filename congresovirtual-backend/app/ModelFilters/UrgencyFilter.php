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

    public function proposal($name) {
        return $this
            ->where('urgencies.proposal_id', '=', $name);
    }

    public function user($name) {
        return $this
            ->where('urgencies.user_id', '=', $name);
    }

    public function proposalIsPublic($name)
    {
        return $this
            ->select('urgencies.*')
            ->join('proposals', 'urgencies.proposal_id', '=', 'proposals.id')
            ->where('proposals.is_public', '=', $name);
    }
}
