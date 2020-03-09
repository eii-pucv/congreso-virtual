<?php

namespace App;

use App\Search\ArticleSearchable;
use App\Observers\ArticleObserver;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use Filterable, ArticleSearchable, SoftDeletes;

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
        static::observe(ArticleObserver::class);
    }

    public static function incrementingCountVotes($params)
    {
        $article = Article::find($params->attributes['article_id']);
        if($article) {
            switch ($params->attributes['vote']) {
                case 0:
                    $article->update(['votos_a_favor' => $article->votos_a_favor + 1]);
                    break;
                case 1:
                    $article->update(['votos_en_contra' => $article->votos_en_contra + 1]);
                    break;
                case 2:
                    $article->update(['abstencion' => $article->abstencion + 1]);
                    break;
                default:
                    throw new \Exception();
            }
        }
    }

    public static function decrementingCountVotes($params)
    {
        $article = Article::find($params->original['article_id']);
        if($article) {
            switch ($params->original['vote']) {
                case 0:
                    $article->update(['votos_a_favor' => $article->votos_a_favor - 1]);
                    break;
                case 1:
                    $article->update(['votos_en_contra' => $article->votos_en_contra - 1]);
                    break;
                case 2:
                    $article->update(['abstencion' => $article->abstencion - 1]);
                    break;
                default:
                    throw new \Exception();
            }
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
