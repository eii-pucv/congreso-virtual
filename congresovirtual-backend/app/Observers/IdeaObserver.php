<?php

namespace App\Observers;

class IdeaObserver
{
    public function deleting($idea)
    {
        if(!$idea->isForceDeleting()) {
            foreach($idea->comments as $comment) {
                $comment->delete();
            }

            $idea->votes()->delete();
        } else {
            $ideaComments = $idea->comments()->withTrashed()->get();
            foreach($ideaComments as $comment) {
                $comment->forceDelete();
            }
        }
    }

    public function restoring($idea)
    {
        $ideaComments = $idea->comments()->withTrashed()->get();
        foreach($ideaComments as $comment) {
            $comment->restore();
        }

        $idea->votes()->restore();
    }
}
