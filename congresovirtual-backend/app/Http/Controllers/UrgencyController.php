<?php

namespace App\Http\Controllers;

use App\Urgency;
use App\Proposal;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UrgencyController extends Controller
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
            if($request->has('proposal_id')) {
                $filter['proposal'] = $request->proposal_id;
            }
            if($request->has('user_id')) {
                $filter['user'] = $request->user_id;
            }

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                $filter['proposalIsPublic'] = true;
            }
            $urgencies = Urgency::filter($filter);
            $totalResults = $urgencies->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $urgencies = $urgencies->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $urgencies = $urgencies
                ->offset($offset)
                ->limit($limit);
            $urgencies = $urgencies->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($urgencies),
                'results' => $urgencies
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: urgencies were not found.'], 412);
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
                'urgency' => 'required|integer|in:1,2',
                'proposal_id' => 'required|integer|unique:urgencies,proposal_id,NULL,NULL,user_id,' . $request->user_id,
                'user_id' => 'required|integer|unique:urgencies,user_id,NULL,NULL,proposal_id,' . $request->proposal_id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $proposal = Proposal::findOrFail($request->proposal_id);
            if(!Auth::user()->hasRole('ADMIN')) {
                if(!$proposal->is_public) {
                    throw new \Exception();
                }
            }

            if($proposal->state == 0 || $proposal->type != $request->urgency) {
                throw new \Exception();
            }

            $user = Auth::user();

            $urgency = new Urgency([
                'urgency' => $request->urgency
            ]);
            $urgency->proposal()->associate($proposal);
            $urgency->user()->associate($user);
            $urgency->save();
            return response()->json([
                'message' => 'Successfully created urgency!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the urgency was not created.'], 412);
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
            $filter = [];
            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                $filter['proposalIsPublic'] = true;
            }
            $urgency = Urgency::filter($filter)
                ->where('urgencies.id', '=', $id)
                ->with(['proposal', 'user'])
                ->firstOrFail();

            return response()->json($urgency, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the urgency was not found.'], 412);
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
                'urgency' => 'required|integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if(Auth::user()->hasRole('ADMIN')) {
                $urgency = Urgency::findOrFail($id);
            } else {
                $urgency = Urgency::where([
                    ['id', $id],
                    ['user_id', Auth::id()]
                ])->firstOrFail();

                if(!$urgency->proposal->is_public) {
                    throw new \Exception();
                }
            }

            $proposal = $urgency->proposal;
            if($proposal->state == 0 || $proposal->type != $request->urgency) {
                throw new \Exception();
            }

            $urgency->fill([
                'urgency' => $request->urgency
            ]);
            $urgency->save();
            return response()->json([
                'message' => 'Successfully updated urgency!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the urgency was not updated.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Urgency::withTrashed()->findOrFail($id)->forceDelete();
            return response()->json([
                'message' => 'Successfully deleted urgency!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the urgency was not deleted.'], 412);
        }
    }
}
