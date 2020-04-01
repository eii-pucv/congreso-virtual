<?php

namespace App\Events\Gamification;

use App\Comment;
use App\Gamification\Action;
use App\Gamification\Event;
use App\Gamification\Reward;
use Closure;

class RegisterCommentEvent
{
    use GamificableEvent;

    /**
     * Handle the gamification event.
     *
     * @param  Comment  $comment
     * @param  Closure  $next
     * @return callable
     */
    public function handle(Comment $comment, Closure $next)
    {
        if(!$this->gamificationIsGloballyEnabled()) {
            return $next(null);
        }

        $player = $this->validatePlayer();
        if(!$player) {
            return $next(null);
        }

        $project = null;
        if($comment->project) {
            $project = $comment->project;
        } else if($comment->article) {
            $project = $comment->article->project;
        } else if($comment->idea) {
            $project = $comment->idea->project;
        } else {
            return $next(null);
        }

        $eventActions = [];
        $eventRewards = [];
        $eventPoints = 0;

        $commentProjectAction = Action::where([
            ['type', 'COMMENT'],
            ['subtype', 'PROJECT']
        ])->first();
        if($commentProjectAction) {
            $eventActions[$commentProjectAction->id] = ['quantity' => 1];
            $eventPoints += $commentProjectAction->points;

            $countActionEvents = Event::where([
                ['events.player_id', $player->user_id],
                ['events.project_id', $project->id],
                ['action_event.action_id', $commentProjectAction->id]
            ])
                ->join('action_event', 'events.id', '=', 'action_event.event_id')
                ->select('action_event.*')
                ->count();

            $reward = Reward::where([
                ['actions_needed', $countActionEvents + 1],
                ['action_id', $commentProjectAction->id]
            ])->first();
            if($reward) {
                $eventRewards[] = ['reward_id' => $reward->id];
                $eventPoints += $reward->points;
            }
        }

        $eventTerms = [];

        if($project->terms) {
            $commentTermAction = Action::where([
                ['type', 'COMMENT'],
                ['subtype', 'TERM']
            ])->first();
            if($commentTermAction) {
                $quantityCommentTermAction = 1;
                foreach ($project->terms->pluck('id') as $eventTermId) {
                    $eventActions[$commentTermAction->id] = ['quantity' => $quantityCommentTermAction];
                    $quantityCommentTermAction++;
                    $eventPoints += $commentTermAction->points;

                    $countActionEvents = Event::where([
                        ['events.player_id', $player->user_id],
                        ['event_reward_term.term_id', $eventTermId],
                        ['action_event.action_id', $commentTermAction->id]
                    ])
                        ->join('event_reward_term', 'events.id', '=', 'event_reward_term.event_id')
                        ->join('action_event', 'events.id', '=', 'action_event.event_id')
                        ->select('action_event.*')
                        ->count();

                    $reward = Reward::where([
                        ['actions_needed', $countActionEvents + 1],
                        ['action_id', $commentTermAction->id]
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
