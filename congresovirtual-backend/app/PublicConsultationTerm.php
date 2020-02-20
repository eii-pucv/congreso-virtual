<?php

namespace App;

use App\Search\Observers\PublicConsultationTermObserver;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PublicConsultationTerm extends Pivot
{
    public $incrementing = true;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'public_consultation_id', 'term_id'
    ];

    protected static function boot()
    {
        parent::boot();
        if(config('services.search.enabled')) {
            static::observe(PublicConsultationTermObserver::class);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publicConsultation()
    {
        return $this->belongsTo('App\PublicConsultation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('App\Term');
    }
}
