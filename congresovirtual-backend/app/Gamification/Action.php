<?php

namespace App\Gamification;

use App\Observers\Gamification\ActionObserver;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use Filterable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'subtype', 'points'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(ActionObserver::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rewards()
    {
        return $this->hasMany('App\Gamification\Reward');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events()
    {
        return $this->belongsToMany('App\Gamification\Event')->withPivot('quantity');
    }
}
