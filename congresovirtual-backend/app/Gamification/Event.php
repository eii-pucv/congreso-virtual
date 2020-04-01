<?php

namespace App\Gamification;

use App\Observers\Gamification\EventObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'points', 'project_id', 'page_id', 'player_id'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(EventObserver::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function actions()
    {
        return $this->belongsToMany('App\Gamification\Action')->withPivot('quantity');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rewards()
    {
        return $this->belongsToMany('App\Gamification\Reward', 'event_reward_term')->with(['action'])->withPivot('term_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function terms()
    {
        return $this->belongsToMany('App\Term', 'event_reward_term')->withPivot('reward_id');
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
    public function page()
    {
        return $this->belongsTo('App\Page');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player()
    {
        return $this->belongsTo('App\Gamification\Player', 'player_id', 'user_id');
    }
}
