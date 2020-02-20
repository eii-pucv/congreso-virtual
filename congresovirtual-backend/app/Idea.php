<?php

namespace App;

use App\Search\IdeaSearchable;
use App\Observers\IdeaObserver;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Idea extends Model
{
    use Filterable, IdeaSearchable, SoftDeletes;

    protected $appends = ['is_public'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titulo', 'detalle', 'votos_a_favor', 'votos_en_contra', 'abstencion', 'project_id'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(IdeaObserver::class);
    }

    public static function incrementingCountVotes($params)
    {
        $idea = Idea::findOrFail($params->attributes['idea_id']);
        switch ($params->attributes['vote']) {
            case 0:
                $idea->update(['votos_a_favor' => $idea->votos_a_favor + 1]);
                break;
            case 1:
                $idea->update(['votos_en_contra' => $idea->votos_en_contra + 1]);
                break;
            case 2:
                $idea->update(['abstencion' => $idea->abstencion + 1]);
                break;
            default:
                throw new \Exception();
        }
    }

    public static function decrementingCountVotes($params)
    {
        $idea = Idea::findOrFail($params->original['idea_id']);
        switch ($params->original['vote']) {
            case 0:
                $idea->update(['votos_a_favor' => $idea->votos_a_favor - 1]);
                break;
            case 1:
                $idea->update(['votos_en_contra' => $idea->votos_en_contra - 1]);
                break;
            case 2:
                $idea->update(['abstencion' => $idea->abstencion - 1]);
                break;
            default:
                throw new \Exception();
        }
    }

    public function getIsPublicAttribute()
    {
        $project = Project::find($this->project_id);
        return $project ? $project->is_public : false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Comment')->with(['user']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes()
    {
        return $this->hasMany('App\Vote');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
