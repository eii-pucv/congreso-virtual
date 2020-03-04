<?php

namespace App;

use EloquentFilter\Filterable;
use App\Search\ProposalSearchable;
use App\Observers\ProposalObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use Filterable, ProposalSearchable, SoftDeletes;

    protected $appends = ['video'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titulo', 'detalle', 'autoria', 'boletin', 'fecha_ingreso', 'state', 'type', 'is_public', 'argument', 'video_code', 'video_source', 'user_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_public' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(ProposalObserver::class);
    }

    public static function incrementingCountUrgencies($params) {
        switch ($params->attributes['urgency']) {
            case 1:
                Proposal::findOrFail($params->attributes['proposal_id'])->increment('petitions');
                break;
            case 2:
                Proposal::findOrFail($params->attributes['proposal_id'])->increment('urgencies');
                break;
            default:
                throw new \Exception();
        }
    }

    public static function decrementingCountUrgencies($params) {
        switch ($params->original['urgency']) {
            case 1:
                Proposal::findOrFail($params->original['proposal_id'])->decrement('petitions');
                break;
            case 2:
                Proposal::findOrFail($params->original['proposal_id'])->decrement('urgencies');
                break;
            default:
                throw new \Exception();
        }
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
    public function urgenciesRelated()
    {
        return $this->hasMany('App\Urgency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
