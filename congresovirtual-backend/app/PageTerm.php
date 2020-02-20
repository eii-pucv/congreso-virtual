<?php

namespace App;

use App\Search\Observers\PageTermObserver;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PageTerm extends Pivot
{
    public $incrementing = true;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'page_id', 'term_id'
    ];

    protected static function boot()
    {
        parent::boot();
        if(config('services.search.enabled')) {
            static::observe(PageTermObserver::class);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page()
    {
        return $this->belongsTo('App\Page');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('App\Term');
    }
}
