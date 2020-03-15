<?php

namespace App\Http\Controllers;

use App\Denounce;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DenounceController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: Only ADMIN type users can use this method (see routes).
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
            if($request->has('comment_id')) {
                $filter['comment'] = $request->comment_id;
            }
            if($request->has('user_id')) {
                $filter['user'] = $request->user_id;
            }

            $denounces = Denounce::filter($filter);
            $totalResults = $denounces->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $denounces = $denounces->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $denounces = $denounces
                ->offset($offset)
                ->limit($limit);
            $denounces = $denounces->with(['comment', 'user'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($denounces),
                'results' => $denounces
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: denounces were not found.'], 412);
        }
    }

    /**
     * Store a newly created resource in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request['user_id'] = Auth::id();
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:191',
                'description' => 'string|max:191|nullable',
                'comment_id' => 'required|integer|unique:denounces,comment_id,NULL,NULL,user_id,' . $request->user_id,
                'user_id' => 'required|integer|unique:denounces,user_id,NULL,NULL,comment_id,' . $request->comment_id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $comment = Comment::findOrFail($request->comment_id);
            $user = Auth::user();

            $denounce = new Denounce([
                'reason' => $request->reason,
                'description' => $request->description
            ]);
            $denounce->comment()->associate($comment);
            $denounce->user()->associate($user);
            $denounce->save();
            return response()->json([
                'message' => 'Successfully created denounce!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the denounce was not created.'], 412);
        }
    }

    /**
     * Display the specified resource.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return response()->json(Denounce::with(['comment', 'user'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the denounce was not found.'], 412);
        }
    }

    /**
     * Update the specified resource in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:191',
                'description' => 'string|max:191|nullable'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if(Auth::user()->hasRole('ADMIN')) {
                $denounce = Denounce::findOrFail($id);
            } else {
                $denounce = Denounce::where([
                    ['id', $id],
                    ['user_id', Auth::id()]
                ])->firstOrFail();
            }

            $denounce->fill([
                'reason' => $request->reason,
                'description' => $request->description
            ]);
            $denounce->save();
            return response()->json([
                'message' => 'Successfully updated denounce!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the denounce was not updated.'], 412);
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

            $denounce = Denounce::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $denounce->forceDelete();
            } else {
                $denounce->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted denounce!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the denounce was not deleted.'], 412);
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
            Denounce::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted denounce!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the denounce was not undeleted.'], 412);
        }
    }

    /**
     * Display the comment associated.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function comment($id)
    {
        try {
            return response()->json(Denounce::findOrFail($id)->comment, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the denounce was not found.'], 412);
        }
    }

    /**
     * Display the user associated.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function user($id)
    {
        try {
            return response()->json(Denounce::findOrFail($id)->user, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the denounce was not found.'], 412);
        }
    }
}
