<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TermUser extends Pivot
{
    public $incrementing = true;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'term_id', 'user_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('App\Term');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
