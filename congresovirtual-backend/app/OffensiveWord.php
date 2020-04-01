<?php

namespace App;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OffensiveWord extends Model
{
    use Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'word'
    ];

    public static function hasOffensiveWord($text)
    {
        $firstOffensiveWordInText = DB::select(
            DB::raw("SELECT * FROM 
                    (SELECT :text AS text) AS text_table, offensive_words 
                    WHERE text_table.text LIKE CONCAT('%', offensive_words.word, '%') 
                    LIMIT 1"),
            ['text' => $text]
        );

        return count($firstOffensiveWordInText) > 0;
    }
}
