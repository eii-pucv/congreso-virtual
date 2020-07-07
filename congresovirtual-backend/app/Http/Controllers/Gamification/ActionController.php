<?php

namespace App\Http\Controllers\Gamification;

use App\Gamification\Action;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ActionController extends Controller
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
            if($request->has('type')) {
                $filter['type'] = $request->type;
            }
            if($request->has('subtype')) {
                $filter['subtype'] = $request->subtype;
            }
            if($request->has('points')) {
                $filter['points'] = $request->points;
            }

            if(Auth::check() && Auth::user()->hasRole('ADMIN') && isset($request->only_trashed) && $request->only_trashed) {
                $actions = Action::filter($filter)->onlyTrashed();
            } else {
                $actions = Action::filter($filter);
            }

            $totalResults = $actions->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $actions = $actions->orderBy($request->order_by, $order);
            }

            if(!isset($request->return_all) || !$request->return_all) {
                $limit = $request->query('limit', 10);
                $offset = $request->query('offset', 0);
                $actions = $actions
                    ->offset($offset)
                    ->limit($limit > 100 ? 100 : $limit);
            }
            $actions = $actions->with(['rewards', 'events'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($actions),
                'results' => $actions
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: actions were not found.'], 412);
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
                'type' => 'required|string|max:191',
                'subtype' => 'string|max:191|nullable',
                'points' => 'integer|min:0'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $action = new Action([
                'type' => strtoupper($request->type),
                'subtype' => isset($request->subtype) ? strtoupper($request->subtype) : null,
                'points' => $request->has('points') ? $request->points : 0
            ]);
            $action->save();
            return response()->json([
                'message' => 'Successfully created action!',
                'data' => $action->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the action was not created.'], 412);
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
            return response()->json(Action::with(['rewards', 'events'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the action was not found.'], 412);
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
                'points' => 'required|integer|min:0'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $action = Action::findOrFail($id);
            $action->fill(['points' => $request->points])->save();
            return response()->json([
                'message' => 'Successfully updated action!',
                'data' => $action->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the action was not updated.'], 412);
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

            $action = Action::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $action->forceDelete();
            } else {
                $action->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted action!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the action was not deleted.'], 412);
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
            Action::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted action!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the action was not undeleted.'], 412);
        }
    }
}
