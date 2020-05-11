<?php

namespace App\Gamification;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventReward extends Pivot
{
    public $incrementing = true;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'reward_id', 'term_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo('App\Gamification\Event');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reward()
    {
        return $this->belongsTo('App\Gamification\Reward');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('App\Term');
    }
}
