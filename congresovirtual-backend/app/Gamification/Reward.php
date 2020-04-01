<?php

namespace App\Gamification;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    use Filterable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'points', 'icon', 'actions_needed', 'action_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events()
    {
        return $this->belongsToMany('App\Gamification\Event', 'event_reward_term')->withPivot('term_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function terms()
    {
        return $this->belongsToMany('App\Term', 'event_reward_term')->withPivot('event_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action()
    {
        return $this->belongsTo('App\Gamification\Action');
    }
}
