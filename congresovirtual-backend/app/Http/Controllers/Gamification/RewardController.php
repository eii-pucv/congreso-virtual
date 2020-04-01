<?php

namespace App\Http\Controllers\Gamification;

use App\Gamification\Reward;
use App\Gamification\Action;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RewardController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $filter = [];
            if($request->has('query')) {
                $filter['query'] = $request['query'];
            }
            if($request->has('name')) {
                $filter['name'] = $request->name;
            }
            if($request->has('points')) {
                $filter['points'] = $request->points;
            }
            if($request->has('actions_neeeded')) {
                $filter['actionsNeeded'] = $request->actions_neeeded;
            }
            if($request->has('action_id')) {
                $filter['action'] = $request->action_id;
            }
            if($request->has('action_type')) {
                $filter['actionType'] = $request->action_type;
            }
            if($request->has('action_subtype')) {
                $filter['actionSubtype'] = $request->action_subtype;
            }

            if(Auth::check() && Auth::user()->hasRole('ADMIN') && isset($request->only_trashed) && $request->only_trashed) {
                $rewards = Reward::filter($filter)->onlyTrashed();
            } else {
                $rewards = Reward::filter($filter);
            }

            $totalResults = $rewards->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $rewards = $rewards->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $rewards = $rewards
                ->offset($offset)
                ->limit($limit);
            $rewards = $rewards->with(['action', 'events', 'terms'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($rewards),
                'results' => $rewards
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: rewards were not found.'], 412);
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
                'name' => 'required|string|max:191',
                'points' => 'integer|min:0',
                'icon' => 'string|max:191|nullable',
                'actions_needed' => 'required|integer|min:0|unique:rewards,actions_needed,NULL,NULL,action_id,' . $request->action_id,
                'action_id' => 'required|integer|unique:rewards,action_id,NULL,NULL,actions_needed,' . $request->actions_needed
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $action = Action::findOrFail($request->action_id);

            $reward = new Reward([
                'name' => $request->name,
                'points' => $request->has('points') ? $request->points : 0,
                'icon' => $request->icon,
                'actions_needed' => $request->actions_needed
            ]);
            $reward->action()->associate($action);
            $reward->save();
            return response()->json([
                'message' => 'Successfully created reward!',
                'data' => $reward->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the reward was not created.'], 412);
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
            return response()->json(Reward::with(['action', 'events', 'terms'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the reward was not found.'], 412);
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
                'name' => 'required|string|max:191',
                'points' => 'required|integer|min:0',
                'icon' => 'string|max:191|nullable'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $reward = Reward::findOrFail($id);
            $reward->fill([
                'name' => $request->name,
                'points' => $request->points,
                'icon' => $request->icon
            ]);
            $reward->save();
            return response()->json([
                'message' => 'Successfully updated reward!',
                'data' => $reward->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the reward was not updated.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
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

            $reward = Reward::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $reward->forceDelete();
            } else {
                $reward->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted reward!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the reward was not deleted.'], 412);
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
            Reward::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted reward!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the reward was not undeleted.'], 412);
        }
    }
}
