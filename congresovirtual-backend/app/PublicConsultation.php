<?php

namespace App;

use EloquentFilter\Filterable;
use App\Search\PublicConsultationSearchable;
use App\Observers\PublicConsultationObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicConsultation extends Model
{
    use Filterable, PublicConsultationSearchable, SoftDeletes;

    protected $appends = ['imagen'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titulo', 'autor', 'estado', 'detalle', 'resumen', 'fecha_inicio', 'fecha_termino', 'votos_a_favor', 'votos_en_contra', 'icono', 'video', 'imagen_id'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(PublicConsultationObserver::class);
    }

    public static function incrementingCountVotes($params)
    {
        $publicConsultation = PublicConsultation::findOrFail($params->attributes['public_consultation_id']);
        switch ($params->attributes['vote']) {
            case 0:
                $publicConsultation->update(['votos_a_favor' => $publicConsultation->votos_a_favor + 1]);
                break;
            case 1:
                $publicConsultation->update(['votos_en_contra' => $publicConsultation->votos_en_contra + 1]);
                break;
            default:
                throw new \Exception();
        }
    }

    public static function decrementingCountVotes($params)
    {
        $publicConsultation = PublicConsultation::findOrFail($params->original['public_consultation_id']);
        switch ($params->original['vote']) {
            case 0:
                $publicConsultation->update(['votos_a_favor' => $publicConsultation->votos_a_favor - 1]);
                break;
            case 1:
                $publicConsultation->update(['votos_en_contra' => $publicConsultation->votos_en_contra - 1]);
                break;
            default:
                throw new \Exception();
        }
    }

    public function getImagenAttribute()
    {
        $file = File::find($this->imagen_id);
        return $file ? "{$file->stored_name}.{$file->extension}" : null;
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function terms()
    {
        return $this->belongsToMany('App\Term')->with(['taxonomies'])->using('App\PublicConsultationTerm');
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
}
