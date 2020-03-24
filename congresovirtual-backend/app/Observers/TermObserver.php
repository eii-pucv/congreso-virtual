<?php

namespace App\Observers;

class TermObserver
{
    public function deleting($term)
    {
        if(!$term->isForceDeleting()) {
            foreach($term->children as $termChild) {
                $termChild->delete();
            }
        } else {
            $termChildren = $term->children()->withTrashed()->get();
            foreach($termChildren as $term) {
                $term->forceDelete();
            }
        }
    }

    public function restoring($term)
    {
        $termChildren = $term->children()->withTrashed()->get();
        foreach($termChildren as $termChild) {
            $termChild->restore();
        }
    }
}
