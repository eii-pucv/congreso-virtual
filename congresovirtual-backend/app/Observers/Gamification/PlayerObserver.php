<?php

namespace App\Observers\Gamification;

use App\Gamification\Player;

class PlayerObserver
{
    public function deleting(Player $player)
    {
        if(!$player->isForceDeleting()) {
            $player->events()->delete();
        }
    }

    public function restoring(Player $player)
    {
        $player->events()->restore();
    }
}
