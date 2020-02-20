<?php

namespace App;

use App\Search\Observers\ProjectTermObserver;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectTerm extends Pivot
{
    public $incrementing = true;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id', 'term_id'
    ];

    protected static function boot()
    {
        parent::boot();
        if(config('services.search.enabled')) {
            static::observe(ProjectTermObserver::class);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('App\Term');
    }
}
