<?php

namespace App\Observers\Gamification;

use App\Gamification\Player;

class EventObserver
{
    public function creating($event)
    {
        Player::increasePoints($event);
    }

    public function updating($event)
    {
        if(isset($event->points)) {
            Player::increasePoints($event);
            Player::decreasePoints($event);
        }
    }

    public function deleting($event)
    {
        Player::decreasePoints($event);
    }
}
