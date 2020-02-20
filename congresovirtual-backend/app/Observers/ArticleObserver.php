<?php

namespace App\Observers;

class ArticleObserver
{
    public function deleting($article)
    {
        if(!$article->isForceDeleting()) {
            foreach($article->comments as $comment) {
                $comment->delete();
            }

            $article->votes()->delete();
        } else {
            $articleComments = $article->comments()->withTrashed()->get();
            foreach($articleComments as $comment) {
                $comment->forceDelete();
            }
        }
    }

    public function restoring($article)
    {
        $articleComments = $article->comments()->withTrashed()->get();
        foreach($articleComments as $comment) {
            $comment->restore();
        }

        $article->votes()->restore();
    }
}
