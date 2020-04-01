<?php

namespace App\Http\Controllers\Gamification;

use App\Gamification\Action;
use App\Gamification\Player;
use App\Gamification\Reward;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: all users can use this method (see routes).
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $filter = [];
            if($request->has('query')) {
                $filter['query'] = $request['query'];
            }
            if($request->has('points')) {
                $filter['points'] = $request->points;
            }
            if($request->has('active_gamification')) {
                $filter['activeGamification'] = $request->active_gamification;
            }
            if($request->has('user_id')) {
                $filter['user'] = $request->user_id;
            }

            $players = Player::filter($filter);
            $totalResults = $players->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $players = $players->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $players = $players
                ->offset($offset)
                ->limit($limit);
            $players = $players->with(['user'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($players),
                'results' => $players
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: players were not found.'], 412);
        }
    }

    /**
     * Store a newly created resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'active_gamification' => 'boolean'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if(Auth::user()->hasRole('ADMIN')) {
                $user = User::findOrFail($request->user_id);
            } else {
                if($request->user_id === Auth::id()) {
                    $user = Auth::user();
                } else {
                    throw new \Exception();
                }
            }

            $player = new Player([
                'active_gamification' => $request->has('active_gamification') ? $request->active_gamification : true
            ]);
            $player->user()->associate($user);
            $player->save();
            return response()->json([
                'message' => 'Successfully created player!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the player was not created.'], 412);
        }
    }

    /**
     * Display the specified resource.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return response()->json(Player::with(['user'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the player was not found.'], 412);
        }
    }

    /**
     * Update the specified resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'active_gamification' => 'required|boolean'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $player = Player::findOrFail($id);
            $player->fill(['active_gamification' => $request->active_gamification])->save();
            return response()->json([
                'message' => 'Successfully updated player!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the player was not updated.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'force_delete' => 'boolean'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $player = Player::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $player->forceDelete();
            } else {
                $player->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted player!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the player was not deleted.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage temporarily.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function undelete($id)
    {
        try {
            Player::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted player!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the player was not undeleted.'], 412);
        }
    }

    /**
     * Display the rewards of player.
     * About access control: all users can use this method (see routes).
     *
     * @param  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function rewards(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_by' => 'string',
                'order' => 'string',
                'limit' => 'integer',
                'offset' => 'integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $rewards = Reward::select(
                'rewards.*',
                DB::raw('COUNT(event_reward_term.id) AS quantity_reward')
            )
                ->join('event_reward_term', 'rewards.id', '=', 'event_reward_term.reward_id')
                ->join('events', 'event_reward_term.event_id', '=', 'events.id')
                ->where('events.player_id', '=', $id)
                ->groupBy('rewards.id');

            $totalResults = count($rewards->get());

            if($request->has('order_by')) {
                $orderBy = $request->query('order_by', 'quantity_reward');
                $order = $request->query('order', 'DESC');
                $rewards = $rewards->orderBy($orderBy, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $rewards = $rewards
                ->offset($offset)
                ->limit($limit);
            $rewards = $rewards->with(['action'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($rewards),
                'results' => $rewards
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: rewards of player were not found.'], 412);
        }
    }

    /**
     * Display the actions of player.
     * About access control: all users can use this method (see routes).
     *
     * @param  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function actions(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_by' => 'string',
                'order' => 'string',
                'limit' => 'integer',
                'offset' => 'integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $actions = Action::select(
                'actions.*',
                DB::raw('CAST(SUM(action_event.quantity) AS UNSIGNED) AS quantity_action')
            )
                ->join('action_event', 'actions.id', '=', 'action_event.action_id')
                ->join('events', 'action_event.event_id', '=', 'events.id')
                ->where('events.player_id', '=', $id)
                ->groupBy('actions.id');

            $totalResults = count($actions->get());

            if($request->has('order_by')) {
                $orderBy = $request->query('order_by', 'quantity_action');
                $order = $request->query('order', 'DESC');
                $actions = $actions->orderBy($orderBy, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $actions = $actions
                ->offset($offset)
                ->limit($limit);
            $actions = $actions->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($actions),
                'results' => $actions
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: actions of player were not found.'], 412);
        }
    }
}
