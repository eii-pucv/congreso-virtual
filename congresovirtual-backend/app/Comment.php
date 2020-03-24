<?php

namespace App;

use App\Observers\CommentObserver;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use Filterable, SoftDeletes;

    protected $appends = ['files'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body', 'perception', 'votos_a_favor', 'votos_en_contra', 'state', 'parent_id', 'project_id', 'article_id', 'idea_id', 'public_consultation_id', 'user_id'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(CommentObserver::class);
    }

    public static function incrementingCountVotes($params)
    {
        $comment = Comment::find($params->attributes['comment_id']);
        if($comment) {
            switch ($params->attributes['vote']) {
                case 0:
                    $comment->update(['votos_a_favor' => $comment->votos_a_favor + 1]);
                    break;
                case 1:
                    $comment->update(['votos_en_contra' => $comment->votos_en_contra + 1]);
                    break;
                default:
                    throw new \Exception();
            }
        }
    }

    public static function decrementingCountVotes($params)
    {
        $comment = Comment::find($params->original['comment_id']);
        if($comment) {
            switch ($params->original['vote']) {
                case 0:
                    $comment->update(['votos_a_favor' => $comment->votos_a_favor - 1]);
                    break;
                case 1:
                    $comment->update(['votos_en_contra' => $comment->votos_en_contra - 1]);
                    break;
                default:
                    throw new \Exception();
            }
        }
    }

    public function getFilesAttribute()
    {
        return $this->files();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function position()
    {
        return $this->hasOne('App\Position');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('App\Comment', 'parent_id')->with(['user']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes()
    {
        return $this->hasMany('App\Vote');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function denounces()
    {
        return $this->hasMany('App\Denounce');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Comment');
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
    public function article()
    {
        return $this->belongsTo('App\Article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function idea()
    {
        return $this->belongsTo('App\Idea');
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
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * @param  $withTrashed
     * @return array
     */
    public function files($withTrashed = false)
    {
        if($withTrashed) {
            return File::withTrashed()
                ->where('object_id', '=', $this->getKey())
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $this->getTable())
                ->get();
        } else {
            return File::where('object_id', '=', $this->getKey())
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $this->getTable())
                ->get();
        }
    }
}
