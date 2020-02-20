<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stopword extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value', 'stopword_type_id', 'object_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stopwordType()
    {
        return $this->belongsTo('App\StopwordType');
    }
}
