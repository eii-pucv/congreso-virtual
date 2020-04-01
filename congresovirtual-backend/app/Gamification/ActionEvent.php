<?php

namespace App\Gamification;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ActionEvent extends Pivot // No usado
{
    public $incrementing = true;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'action_id', 'event_id', 'quantity'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action()
    {
        return $this->belongsTo('App\Gamification\Action');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo('App\Gamification\Event');
    }
}
