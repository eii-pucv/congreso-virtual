<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TaxonomyTerm extends Pivot
{
    public $incrementing = true;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'taxonomy_id', 'term_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonomy()
    {
        return $this->belongsTo('App\Taxonomy');
    }

    public function term()
    {
        return $this->belongsTo('App\Term');
    }
}
