<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StopwordType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label', 'table_name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stopwords()
    {
        return $this->hasMany('App\Stopword');
    }
}
