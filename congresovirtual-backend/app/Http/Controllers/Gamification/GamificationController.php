<?php

namespace App\Http\Controllers\Gamification;

use App\Gamification\Player;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GamificationController extends Controller
{
    /**
     * Display a ranking according to players reward for a term.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rankingTermPlayersRewards(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'term_id' => 'required|integer',
                'order_by' => 'string',
                'order' => 'string',
                'limit' => 'integer',
                'offset' => 'integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $ranking = Player::select(
                'players.user_id',
                'users.name',
                'users.surname',
                // 'users.email',
                DB::raw('CONCAT(files.stored_name, \'.\', files.extension) AS avatar'),
                DB::raw('CAST(SUM(rewards.points) AS UNSIGNED) AS term_total_points'),
                DB::raw('COUNT(events.id) AS events_quantity')
            )
                ->join('users', 'players.user_id', '=', 'users.id')
                ->leftJoin('files', 'users.avatar_id', '=', 'files.id')
                ->join('events', 'players.user_id', '=', 'events.player_id')
                ->join('event_reward_term', 'events.id', '=', 'event_reward_term.event_id')
                ->join('rewards', 'event_reward_term.reward_id', '=', 'rewards.id')
                ->where([
                    ['players.active_gamification', true],
                    ['event_reward_term.term_id', $request->term_id],
                ])
                ->groupBy('players.user_id')->has('user');

            $totalResults = count($ranking->get());

            if($request->has('order_by')) {
                $orderBy = $request->query('order_by', 'term_total_points');
                $order = $request->query('order', 'DESC');
                $ranking = $ranking->orderBy($orderBy, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $ranking = $ranking
                ->offset($offset)
                ->limit($limit > 100 ? 100 : $limit);
            $ranking = $ranking->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($ranking),
                'results' => $ranking
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the ranking could not be generated.'], 412);
        }
    }

    /**
     * Display a ranking according to players reward for a project.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rankingProjectPlayersRewards(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required|integer',
                'order_by' => 'string',
                'order' => 'string',
                'limit' => 'integer',
                'offset' => 'integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $ranking = Player::select(
                'players.user_id',
                'users.name',
                'users.surname',
                // 'users.email',
                DB::raw('CONCAT(files.stored_name, \'.\', files.extension) AS avatar'),
                DB::raw('CAST(SUM(rewards.points) AS UNSIGNED) AS project_total_points'),
                DB::raw('COUNT(events.id) AS events_quantity')
            )
                ->join('users', 'players.user_id', '=', 'users.id')
                ->leftJoin('files', 'users.avatar_id', '=', 'files.id')
                ->join('events', 'players.user_id', '=', 'events.player_id')
                ->join('event_reward_term', 'events.id', '=', 'event_reward_term.event_id')
                ->join('rewards', 'event_reward_term.reward_id', '=', 'rewards.id')
                ->join('actions', 'rewards.action_id', '=', 'actions.id')
                ->where([
                    ['players.active_gamification', true],
                    ['events.project_id', $request->project_id],
                    ['actions.subtype', 'PROJECT']
                ])
                ->groupBy('players.user_id')->has('user');

            $totalResults = count($ranking->get());

            if($request->has('order_by')) {
                $orderBy = $request->query('order_by', 'project_total_points');
                $order = $request->query('order', 'DESC');
                $ranking = $ranking->orderBy($orderBy, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $ranking = $ranking
                ->offset($offset)
                ->limit($limit > 100 ? 100 : $limit);
            $ranking = $ranking->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($ranking),
                'results' => $ranking
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the ranking could not be generated.'], 412);
        }
    }

    /**
     * Display a ranking according to players reward for a page.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rankingPagePlayersRewards(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'page_id' => 'required|integer',
                'order_by' => 'string',
                'order' => 'string',
                'limit' => 'integer',
                'offset' => 'integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $ranking = Player::select(
                'players.user_id',
                'users.name',
                'users.surname',
                // 'users.email',
                DB::raw('CONCAT(files.stored_name, \'.\', files.extension) AS avatar'),
                DB::raw('CAST(SUM(rewards.points) AS UNSIGNED) AS page_total_points'),
                DB::raw('COUNT(events.id) AS events_quantity')
            )
                ->join('users', 'players.user_id', '=', 'users.id')
                ->leftJoin('files', 'users.avatar_id', '=', 'files.id')
                ->join('events', 'players.user_id', '=', 'events.player_id')
                ->join('event_reward_term', 'events.id', '=', 'event_reward_term.event_id')
                ->join('rewards', 'event_reward_term.reward_id', '=', 'rewards.id')
                ->join('actions', 'rewards.action_id', '=', 'actions.id')
                ->where([
                    ['players.active_gamification', true],
                    ['events.page_id', $request->page_id],
                    ['actions.subtype', 'PAGE']
                ])
                ->groupBy('players.user_id')->has('user');

            $totalResults = count($ranking->get());

            if($request->has('order_by')) {
                $orderBy = $request->query('order_by', 'page_total_points');
                $order = $request->query('order', 'DESC');
                $ranking = $ranking->orderBy($orderBy, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $ranking = $ranking
                ->offset($offset)
                ->limit($limit > 100 ? 100 : $limit);
            $ranking = $ranking->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($ranking),
                'results' => $ranking
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the ranking could not be generated.'], 412);
        }
    }

    /**
     * Display a ranking according to players reward and action for a term.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rankingTermPlayersRewardsAndActions(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'term_id' => 'required|integer',
                'order_by' => 'string',
                'order' => 'string',
                'limit' => 'integer',
                'offset' => 'integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $ranking = Player::select(
                'players.user_id',
                'users.name',
                'users.surname',
                // 'users.email',
                DB::raw('CONCAT(files.stored_name, \'.\', files.extension) AS avatar'),
                DB::raw('CAST(COALESCE(SUM(rewards.points), 0) AS UNSIGNED) AS term_reward_points'),
                DB::raw('CAST(COALESCE(SUM(actions.points), 0) AS UNSIGNED) AS term_action_points'),
                DB::raw('CAST((COALESCE(SUM(rewards.points), 0) + COALESCE(SUM(actions.points), 0)) AS UNSIGNED) AS term_total_points'),
                DB::raw('COUNT(events.id) AS events_quantity')
            )
                ->join('users', 'players.user_id', '=', 'users.id')
                ->leftJoin('files', 'users.avatar_id', '=', 'files.id')
                ->join('events', 'players.user_id', '=', 'events.player_id')
                ->join('event_reward_term', 'events.id', '=', 'event_reward_term.event_id')
                ->leftJoin('rewards', 'event_reward_term.reward_id', '=', 'rewards.id')
                ->join('action_event', 'events.id', '=', 'action_event.event_id')
                ->join('actions', 'action_event.action_id', '=', 'actions.id')
                ->where([
                    ['players.active_gamification', true],
                    ['event_reward_term.term_id', $request->term_id],
                    ['actions.subtype', 'TERM']
                ])
                ->groupBy('players.user_id')->has('user');

            $totalResults = count($ranking->get());

            if($request->has('order_by')) {
                $orderBy = $request->query('order_by', 'term_total_points');
                $order = $request->query('order', 'DESC');
                $ranking = $ranking->orderBy($orderBy, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $ranking = $ranking
                ->offset($offset)
                ->limit($limit > 100 ? 100 : $limit);
            $ranking = $ranking->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($ranking),
                'results' => $ranking
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the ranking could not be generated.'], 412);
        }
    }

    /**
     * Display a ranking according to players reward and action for a project.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rankingProjectPlayersRewardsAndActions(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required|integer',
                'order_by' => 'string',
                'order' => 'string',
                'limit' => 'integer',
                'offset' => 'integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $firstQuery = Player::select(
                'players.user_id',
                'users.name',
                'users.surname',
                // 'users.email',
                DB::raw('CONCAT(files.stored_name, \'.\', files.extension) AS avatar'),
                DB::raw('CAST(SUM(actions.points) AS UNSIGNED) AS project_action_points')
            )
                ->join('users', 'players.user_id', '=', 'users.id')
                ->leftJoin('files', 'users.avatar_id', '=', 'files.id')
                ->join('events', 'players.user_id', '=', 'events.player_id')
                ->join('action_event', 'events.id', '=', 'action_event.event_id')
                ->join('actions', 'action_event.action_id', '=', 'actions.id')
                ->where([
                    ['players.active_gamification', true],
                    ['events.project_id', $request->project_id],
                    ['actions.subtype', 'PROJECT']
                ])
                ->groupBy('players.user_id')->has('user');

            $secondQuery = Player::select(
                'players.user_id',
                DB::raw('CAST(SUM(rewards.points) AS UNSIGNED) AS project_reward_points')
            )
                ->join('events', 'players.user_id', '=', 'events.player_id')
                ->join('event_reward_term', 'events.id', '=', 'event_reward_term.event_id')
                ->leftJoin('rewards', 'event_reward_term.reward_id', '=', 'rewards.id')
                ->join('actions', 'rewards.action_id', '=', 'actions.id')
                ->where([
                    ['players.active_gamification', true],
                    ['events.project_id', $request->project_id],
                    ['actions.subtype', 'PROJECT']
                ])
                ->groupBy('players.user_id')->has('user');

            $ranking = DB::table(DB::raw("({$firstQuery->toSql()}) AS a"))
                ->mergeBindings($firstQuery->getQuery())
                ->select(
                    'a.user_id',
                    'a.name',
                    'a.surname',
                    // 'a.email',
                    'a.avatar',
                    'a.project_action_points',
                    'b.project_reward_points',
                    DB::raw('CAST(SUM(a.project_action_points + b.project_reward_points) AS UNSIGNED) AS project_total_points')
                )
                ->join(DB::raw("({$secondQuery->toSql()}) AS b"), 'a.user_id', '=', 'b.user_id')
                ->mergeBindings($secondQuery->getQuery())
                ->groupBy('a.user_id');

            $totalResults = count($ranking->get());

            if($request->has('order_by')) {
                $orderBy = $request->query('order_by', 'project_total_points');
                $order = $request->query('order', 'DESC');
                $ranking = $ranking->orderBy($orderBy, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $ranking = $ranking
                ->offset($offset)
                ->limit($limit > 100 ? 100 : $limit);
            $ranking = $ranking->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($ranking),
                'results' => $ranking
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the ranking could not be generated.'], 412);
        }
    }

    /**
     * Display a ranking according to players reward and action for a page.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rankingPagePlayersRewardsAndActions(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'page_id' => 'required|integer',
                'order_by' => 'string',
                'order' => 'string',
                'limit' => 'integer',
                'offset' => 'integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $firstQuery = Player::select(
                'players.user_id',
                'users.name',
                'users.surname',
                // 'users.email',
                DB::raw('CONCAT(files.stored_name, \'.\', files.extension) AS avatar'),
                DB::raw('CAST(SUM(actions.points) AS UNSIGNED) AS page_action_points')
            )
                ->join('users', 'players.user_id', '=', 'users.id')
                ->leftJoin('files', 'users.avatar_id', '=', 'files.id')
                ->join('events', 'players.user_id', '=', 'events.player_id')
                ->join('action_event', 'events.id', '=', 'action_event.event_id')
                ->join('actions', 'action_event.action_id', '=', 'actions.id')
                ->where([
                    ['players.active_gamification', true],
                    ['events.page_id', $request->page_id],
                    ['actions.subtype', 'PAGE']
                ])
                ->groupBy('players.user_id')->has('user');

            $secondQuery = Player::select(
                'players.user_id',
                DB::raw('CAST(SUM(rewards.points) AS UNSIGNED) AS page_reward_points')
            )
                ->join('events', 'players.user_id', '=', 'events.player_id')
                ->join('event_reward_term', 'events.id', '=', 'event_reward_term.event_id')
                ->leftJoin('rewards', 'event_reward_term.reward_id', '=', 'rewards.id')
                ->join('actions', 'rewards.action_id', '=', 'actions.id')
                ->where([
                    ['players.active_gamification', true],
                    ['events.page_id', $request->page_id],
                    ['actions.subtype', 'PAGE']
                ])
                ->groupBy('players.user_id')->has('user');

            $ranking = DB::table(DB::raw("({$firstQuery->toSql()}) AS a"))
                ->mergeBindings($firstQuery->getQuery())
                ->select(
                    'a.user_id',
                    'a.name',
                    'a.surname',
                    // 'a.email',
                    'a.avatar',
                    'a.page_action_points',
                    'b.page_reward_points',
                    DB::raw('CAST(SUM(a.page_action_points + b.page_reward_points) AS UNSIGNED) AS page_total_points')
                )
                ->join(DB::raw("({$secondQuery->toSql()}) AS b"), 'a.user_id', '=', 'b.user_id')
                ->mergeBindings($secondQuery->getQuery())
                ->groupBy('a.user_id');

            $totalResults = count($ranking->get());

            if($request->has('order_by')) {
                $orderBy = $request->query('order_by', 'page_total_points');
                $order = $request->query('order', 'DESC');
                $ranking = $ranking->orderBy($orderBy, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $ranking = $ranking
                ->offset($offset)
                ->limit($limit > 100 ? 100 : $limit);
            $ranking = $ranking->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($ranking),
                'results' => $ranking
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the ranking could not be generated.'], 412);
        }
    }
}
