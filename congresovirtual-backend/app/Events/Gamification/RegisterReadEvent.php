<?php

namespace App\Events\Gamification;

use App\Gamification\Action;
use App\Gamification\Event;
use App\Gamification\Reward;
use Closure;

class RegisterReadEvent
{
    use GamificableEvent;

    /**
     * Handle the gamification event.
     *
     * @param  object  $read
     * @param  Closure  $next
     * @return callable
     */
    public function handle($read, Closure $next)
    {
        if(!$this->gamificationIsGloballyEnabled()) {
            return $next(null);
        }

        $player = $this->validatePlayer();
        if(!$player) {
            return $next(null);
        }

        $project = null;
        $page = null;
        $subtypeAction = null;
        if(isset($read->project)) {
            $project = $read->project;
            $subtypeAction = 'PROJECT';
        } else if(isset($read->article)) {
            $project = $read->article->project;
            $subtypeAction = 'PROJECT';
        } else if(isset($read->idea)) {
            $project = $read->idea->project;
            $subtypeAction = 'PROJECT';
        } else if(isset($read->page)) {
            $page = $read->page;
            $subtypeAction = 'PAGE';
        } else {
            return $next(null);
        }

        $eventActions = [];
        $eventRewards = [];
        $eventPoints = 0;

        $readProjectOrPageAction = Action::where([
            ['type', 'READ'],
            ['subtype', $subtypeAction]
        ])->first();
        if($readProjectOrPageAction) {
            $eventActions[$readProjectOrPageAction->id] = ['quantity' => 1];
            $eventPoints += $readProjectOrPageAction->points;

            $countActionEvents = Event::where([
                ['events.player_id', $player->user_id],
                ['events.project_id', $project ? $project->id : null],
                ['events.page_id', $page ? $page->id : null],
                ['action_event.action_id', $readProjectOrPageAction->id]
            ])
                ->join('action_event', 'events.id', '=', 'action_event.event_id')
                ->select('action_event.*')
                ->count();

            $reward = Reward::where([
                ['actions_needed', $countActionEvents + 1],
                ['action_id', $readProjectOrPageAction->id]
            ])->first();
            if($reward) {
                $eventRewards[] = ['reward_id' => $reward->id];
                $eventPoints += $reward->points;
            }
        }

        $eventTerms = [];

        if(($project && $project->terms) || ($page && $page->terms)) {
            $readTermAction = Action::where([
                ['type', 'READ'],
                ['subtype', 'TERM']
            ])->first();
            if($readTermAction) {
                if($project && $project->terms) {
                    $eventTermsIds = $project->terms->pluck('id');
                } else {
                    $eventTermsIds = $page->terms->pluck('id');
                }

                $quantityReadTermAction = 1;
                foreach ($eventTermsIds as $eventTermId) {
                    $eventActions[$readTermAction->id] = ['quantity' => $quantityReadTermAction];
                    $quantityReadTermAction++;
                    $eventPoints += $readTermAction->points;

                    $countActionEvents = Event::where([
                        ['events.player_id', $player->user_id],
                        ['event_reward_term.term_id', $eventTermId],
                        ['action_event.action_id', $readTermAction->id]
                    ])
                        ->join('event_reward_term', 'events.id', '=', 'event_reward_term.event_id')
                        ->join('action_event', 'events.id', '=', 'action_event.event_id')
                        ->select('action_event.*')
                        ->count();

                    $reward = Reward::where([
                        ['actions_needed', $countActionEvents + 1],
                        ['action_id', $readTermAction->id]
                    ])->first();
                    if($reward) {
                        $eventRewards[] = [
                            'reward_id' => $reward->id,
                            'term_id' => $eventTermId
                        ];
                        $eventPoints += $reward->points;
                    } else {
                        $eventTerms[] = $eventTermId;
                    }
                }
            }
        }

        $playerEvent = new Event([
            'points' => $eventPoints
        ]);
        $playerEvent->project()->associate($project);
        $playerEvent->page()->associate($page);
        $playerEvent->player()->associate($player);
        $playerEvent->save();

        $playerEvent->actions()->sync($eventActions);
        $playerEvent->rewards()->sync($eventRewards);
        $playerEvent->terms()->sync($eventTerms, false);

        return $next($this->generateResult($playerEvent));
    }
}
