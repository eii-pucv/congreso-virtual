<?php

namespace App\Gamification;

use App\Observers\Gamification\PlayerObserver;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use Filterable, SoftDeletes;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'points', 'active_gamification'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active_gamification' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(PlayerObserver::class);
    }

    public static function increasePoints($params)
    {
        $player = Player::findOrFail($params->attributes['player_id']);
        $player->update(['points' => $player->points + $params->attributes['points']]);
    }

    public static function decreasePoints($params)
    {
        $player = Player::findOrFail($params->original['player_id']);
        $player->update(['points' => $player->points + $params->original['points']]);

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany('App\Gamification\Event', 'player_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
