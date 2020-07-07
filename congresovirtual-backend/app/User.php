<?php

namespace App;

use App\Traits\UserMetadateable;
use App\Observers\UserObserver;
use Laravel\Passport\HasApiTokens;
use EloquentFilter\Filterable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements MustVerifyEmail
{
    use UserMetadateable, HasApiTokens, Filterable, Notifiable, SoftDeletes;

    protected $appends = ['avatar', 'active_gamification'];
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'surname', 'rol', 'email', 'password', 'active', 'activation_token', 'avatar_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'activation_token', 'remember_token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['active' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::observe(UserObserver::class);
    }

    public function isActive()
    {
        return (boolean) $this->active;
    }

    public function hasRole($role)
    {
        return $this->rol === $role;
    }

    public function getAvatarAttribute()
    {
        $file = File::find($this->avatar_id);
        return $file ? "{$file->stored_name}.{$file->extension}" : null;
    }

    public function getActiveGamificationAttribute()
    {
        $player = $this->player;
        return $player ? $player->active_gamification : false;
    }

    public function getEmailAttribute($value) {
        if(Auth::check() && (Auth::user()->hasRole('ADMIN') || Auth::id() === $this->id)) {
            return $value;
        } else {
            return null;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function player()
    {
        return $this->hasOne('App\Gamification\Player');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locationOrgs()
    {
        return $this->hasMany('App\LocationOrg');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function memberOrgs()
    {
        return $this->hasMany('App\MemberOrg');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Comment');
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
    public function urgencies()
    {
        return $this->hasMany('App\Urgency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposals()
    {
        return $this->hasMany('App\Proposal');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function denounces()
    {
        return $this->hasMany('App\Denounce');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userMetas()
    {
        return $this->hasMany('App\UserMeta');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function terms()
    {
        return $this->belongsToMany('App\Term');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatarRelated()
    {
        return $this->belongsTo('App\File', 'avatar_id');
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
