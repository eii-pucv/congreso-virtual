<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

    protected $appends = ['original_filename', 'stored_filename', 'filetype'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'original_name', 'stored_name', 'extension', 'file_type_id', 'object_id'
    ];

    public function getOriginalFilenameAttribute()
    {
        return "{$this->original_name}.{$this->extension}";
    }

    public function getStoredFilenameAttribute()
    {
        return "{$this->stored_name}.{$this->extension}";
    }

    public function getFiletypeAttribute()
    {
        $fileType = FileType::find($this->file_type_id);
        return $fileType ? $fileType->value : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fileType()
    {
        return $this->belongsTo('App\FileType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function project()
    {
        return $this->hasOne('App\Project', 'imagen_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function publicConsultation()
    {
        return $this->hasOne('App\PublicConsultation', 'imagen_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User', 'avatar_id');
    }
}
