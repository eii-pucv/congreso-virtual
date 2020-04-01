<?php

namespace App\Observers\Gamification;

use App\Gamification\Action;

class ActionObserver
{
    public function deleting(Action $action)
    {
        if(!$action->isForceDeleting()) {
            $action->rewards()->delete();
        }
    }

    public function restoring(Action $action)
    {
        $action->rewards()->restore();
    }
}
