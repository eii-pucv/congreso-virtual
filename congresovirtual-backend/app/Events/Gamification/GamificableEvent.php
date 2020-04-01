<?php

namespace App\Events\Gamification;

use App\Gamification\Event;
use App\Setting;
use Illuminate\Support\Facades\Auth;
use Closure;

trait GamificableEvent
{
    public function gamificationIsGloballyEnabled()
    {
        $activeGamificationSetting = Setting::where('key', 'active_gamification')->first();
        if(!$activeGamificationSetting || !json_decode($activeGamificationSetting->value)) {
            return false;
        }
        return true;
    }

    public function validatePlayer()
    {
        $user = Auth::user();
        if(!$user || !$user->player || !$user->player->active_gamification) {
            return null;
        }
        return $user->player;
    }

    public function generateResult(Event $playerEvent)
    {
        $result = $playerEvent->refresh()->toArray();
        $result['terms'] = $playerEvent->terms;
        $result['actions'] = $playerEvent->actions;
        $result['rewards'] = $playerEvent->rewards;

        return $result;
    }
}
