<?php

namespace App;

use EloquentFilter\Filterable;
use App\Search\ProjectSearchable;
use App\Observers\ProjectObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use Filterable, ProjectSearchable, SoftDeletes;

    protected $appends = ['imagen', 'video'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titulo', 'postulante', 'estado', 'etapa', 'detalle', 'resumen', 'fecha_inicio', 'fecha_termino', 'boletin',
        'is_principal', 'is_public', 'is_enabled', 'votos_a_favor', 'votos_en_contra', 'abstencion', 'video_code', 'video_source', 'imagen_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_principal' => 'boolean',
        'is_public' => 'boolean',
        'is_enabled' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(ProjectObserver::class);
    }

    public static function incrementingCountVotes($params)
    {
        $project = Project::find($params->attributes['project_id']);
        if($project) {
            switch ($params->attributes['vote']) {
                case 0:
                    $project->update(['votos_a_favor' => $project->votos_a_favor + 1]);
                    break;
                case 1:
                    $project->update(['votos_en_contra' => $project->votos_en_contra + 1]);
                    break;
                case 2:
                    $project->update(['abstencion' => $project->abstencion + 1]);
                    break;
                default:
                    throw new \Exception();
            }
        }
    }

    public static function decrementingCountVotes($params)
    {
        $project = Project::find($params->original['project_id']);
        if($project) {
            switch ($params->original['vote']) {
                case 0:
                    $project->update(['votos_a_favor' => $project->votos_a_favor - 1]);
                    break;
                case 1:
                    $project->update(['votos_en_contra' => $project->votos_en_contra - 1]);
                    break;
                case 2:
                    $project->update(['abstencion' => $project->abstencion - 1]);
                    break;
                default:
                    throw new \Exception();
            }
        }
    }

    public function getImagenAttribute()
    {
        $file = File::find($this->imagen_id);
        return $file ? "{$file->stored_name}.{$file->extension}" : null;
    }

    public function getVideoAttribute()
    {
        if($this->video_code && $this->video_source) {
            $iframes = json_decode(Setting::where('key', 'video_iframes')->first()->value);
            $iframe = isset($iframes->{$this->video_source}) ? $iframes->{$this->video_source} : null;
            if($iframe) {
                return str_replace('[VIDEO_CODE]', $this->video_code, $iframe);
            }
        }
        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany('App\Article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ideas()
    {
        return $this->hasMany('App\Idea');
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function topicModel()
    {
        return $this->hasOne('App\TopicModel');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function terms()
    {
        return $this->belongsToMany('App\Term')->with(['taxonomies'])->using('App\ProjectTerm');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imagenRelated()
    {
        return $this->belongsTo('App\File', 'imagen_id');
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

    public function stopwords()
    {
        return Stopword::where('object_id', '=', $this->getKey())
            ->join('stopword_types', 'stopwords.stopword_type_id', '=', 'stopword_types.id')
            ->select('stopwords.*')
            ->where('stopword_types.table_name', $this->getTable())
            ->get();
    }
}
