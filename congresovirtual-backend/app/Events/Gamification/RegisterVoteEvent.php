<?php

namespace App\Events\Gamification;

use App\Vote;
use App\Gamification\Action;
use App\Gamification\Event;
use App\Gamification\Reward;
use Closure;

class RegisterVoteEvent
{
    use GamificableEvent;

    /**
     * Handle the gamification event.
     *
     * @param  Vote  $vote
     * @param  Closure  $next
     * @return callable
     */
    public function handle(Vote $vote, Closure $next)
    {
        if(!$this->gamificationIsGloballyEnabled()) {
            return $next(null);
        }

        $player = $this->validatePlayer();
        if(!$player) {
            return $next(null);
        }

        $project = null;
        if($vote->project) {
            $project = $vote->project;
        } else if($vote->article) {
            $project = $vote->article->project;
        } else if($vote->idea) {
            $project = $vote->idea->project;
        } else {
            return $next(null);
        }

        $eventActions = [];
        $eventRewards = [];
        $eventPoints = 0;

        $voteProjectAction = Action::where([
            ['type', 'VOTE'],
            ['subtype', 'PROJECT']
        ])->first();
        if($voteProjectAction) {
            $eventActions[$voteProjectAction->id] = ['quantity' => 1];
            $eventPoints += $voteProjectAction->points;

            $countActionEvents = Event::where([
                ['events.player_id', $player->user_id],
                ['events.project_id', $project->id],
                ['action_event.action_id', $voteProjectAction->id]
            ])
                ->join('action_event', 'events.id', '=', 'action_event.event_id')
                ->select('action_event.*')
                ->count();

            $reward = Reward::where([
                ['actions_needed', $countActionEvents + 1],
                ['action_id', $voteProjectAction->id]
            ])->first();
            if($reward) {
                $eventRewards[] = ['reward_id' => $reward->id];
                $eventPoints += $reward->points;
            }
        }

        $eventTerms = [];

        if($project->terms) {
            $voteTermAction = Action::where([
                ['type', 'VOTE'],
                ['subtype', 'TERM']
            ])->first();
            if($voteTermAction) {
                $eventActionsIds[] = $voteTermAction->id;

                $quantityVoteTermAction = 1;
                foreach ($project->terms->pluck('id') as $eventTermId) {
                    $eventActions[$voteTermAction->id] = ['quantity' => $quantityVoteTermAction];
                    $quantityVoteTermAction++;
                    $eventPoints += $voteTermAction->points;

                    $countActionEvents = Event::where([
                        ['events.player_id', $player->user_id],
                        ['event_reward_term.term_id', $eventTermId],
                        ['action_event.action_id', $voteTermAction->id]
                    ])
                        ->join('event_reward_term', 'events.id', '=', 'event_reward_term.event_id')
                        ->join('action_event', 'events.id', '=', 'action_event.event_id')
                        ->select('action_event.*')
                        ->count();

                    $reward = Reward::where([
                        ['actions_needed', $countActionEvents + 1],
                        ['action_id', $voteTermAction->id]
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
        $playerEvent->player()->associate($player);
        $playerEvent->save();

        $playerEvent->actions()->sync($eventActions);
        $playerEvent->rewards()->sync($eventRewards);
        $playerEvent->terms()->sync($eventTerms, false);

        return $next($this->generateResult($playerEvent));
    }
}
