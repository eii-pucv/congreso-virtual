<?php

namespace App\Observers;

use App\File;

class CommentObserver
{
    public function deleting($comment)
    {
        $commentFiles = $comment->files(true)->pluck('id');

        if(!$comment->isForceDeleting()) {
            foreach($comment->children as $commentChild) {
                $commentChild->delete();
            }

            $comment->position()->delete();
            $comment->votes()->delete();
            $comment->denounces()->delete();
            File::whereIn('id', $commentFiles)->delete();
        } else {
            $commentChildren = $comment->children()->withTrashed()->get();
            foreach($commentChildren as $comment) {
                $comment->forceDelete();
            }

            File::whereIn('id', $commentFiles)->forceDelete();
            $commentDirectory = "comments/{$comment->id}";
            \Storage::deleteDirectory($commentDirectory);
        }
    }

    public function restoring($comment)
    {
        $commentChildren = $comment->children()->withTrashed()->get();
        foreach($commentChildren as $commentChild) {
            $commentChild->restore();
        }

        $comment->position()->restore();
        $comment->votes()->restore();
        $comment->denounces()->restore();
        $commentFiles = $comment->files(true)->pluck('id');
        File::withTrashed()->whereIn('id', $commentFiles)->restore();
    }
}
