<?php

namespace App\Http\Controllers;

use App\Proposal;
use App\Urgency;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProposalController extends Controller
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
            if($request->has('titulo')) {
                $filter['titulo'] = $request->titulo;
            }
            if($request->has('detalle')) {
                $filter['detalle'] = $request->detalle;
            }
            if($request->has('autoria')) {
                $filter['autoria'] = $request->autoria;
            }
            if($request->has('boletin')) {
                $filter['boletin'] = $request->boletin;
            }
            if($request->has('argument')) {
                $filter['argument'] = $request->argument;
            }
            if($request->has('state')) {
                $filter['state'] = $request->state;
            }
            if($request->has('type')) {
                $filter['type'] = $request->type;
            }
            if($request->has('user_id')) {
                $filter['user'] = $request->user_id;
            }

            if(Auth::check() && Auth::user()->hasRole('ADMIN')) {
                $filter['isPublic'] = $request->query('is_public', null);
            } else {
                $filter['isPublic'] = true;
            }

            $proposals = Proposal::filter($filter);
            $totalResults = $proposals->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $proposals = $proposals->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $proposals = $proposals
                ->offset($offset)
                ->limit($limit);
            $proposals = $proposals->with(['user', 'urgenciesRelated'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($proposals),
                'results' => $proposals
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: proposals were not found.'], 412);
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
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string',
                'detalle' => 'required|string',
                'autoria' => 'required|string',
                'boletin' => 'required|string|max:191|unique:proposals',
                'fecha_ingreso' => 'date_format:Y-m-d|nullable',
                'type' => 'required|integer|in:1,2',
                'state' => 'integer|in:0,1',
                'is_public' => 'boolean',
                'argument' => 'string|nullable',
                'video_code' => 'string|max:191|nullable',
                'video_source' => 'string|max:191|nullable',
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $user = Auth::user();

            $state = 0;
            $isPublic = false;
            $argument = null;
            $videoCode = null;
            $videoSource = null;
            if($user->hasRole('ADMIN') && $request->has('state')) {
                $userProposals = User::findOrFail($user->id)->proposals;
                if($request->state == 1 && $userProposals->where('state', '=', 1)->first()) {
                    throw new \Exception();
                } else if($request->state == 1) {
                    $state = 1;
                    $isPublic = $request->has('is_public') ? $request->is_public : $isPublic;
                    $argument = $request->argument;
                    $videoCode = $request->video_code;
                    $videoSource = $request->video_source;
                }
            }

            $proposal = new Proposal([
                'titulo' => $request->titulo,
                'detalle' => $request->detalle,
                'autoria' => $request->autoria,
                'boletin' => $request->boletin,
                'fecha_ingreso' => $request->fecha_ingreso,
                'type' => $request->type,
                'state' => $state,
                'is_public' => $isPublic,
                'argument' => $argument,
                'video_code' => $videoCode,
                'video_source' => $videoSource
            ]);
            $proposal->user()->associate($user);
            $proposal->save();
            return response()->json([
                'message' => 'Successfully created proposal!',
                'data' => $proposal->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the proposal was not created.'], 412);
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
            $proposal = Proposal::with(['user', 'urgenciesRelated'])->findOrFail($id);

            if(!$proposal->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($proposal, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the proposal was not found.'], 412);
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
                'detalle' => 'string',
                'type' => 'integer|in:1,2',
                'state' => 'integer|in:0,1',
                'is_public' => 'boolean',
                'argument' => 'string|nullable',
                'video_code' => 'string|max:191|nullable',
                'video_source' => 'string|max:191|nullable',
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if(Auth::user()->hasRole('ADMIN')) {
                $proposal = Proposal::findOrFail($id);

                $state = $proposal->state;
                if($request->has('state')) {
                    if($request->state == 1 && $proposal->user->proposals->whereNotIn('id', $proposal->id)->where('state', '=', 1)->first()) {
                        throw new \Exception();
                    }
                    $state = $request->state;
                }

                $argument = $proposal->argument;
                $videoCode = $proposal->video_code;
                $videoSource = $proposal->video_source;
                if($state == 1) {
                    if(isset($request->argument)) {
                        $argument = $request->argument;
                    }
                    if(isset($request->video_code)) {
                        $videoCode = $request->video_code;
                    }
                    if(isset($request->video_source)) {
                        $videoSource = $request->video_source;
                    }
                } else if($state == 0 && (isset($request->argument) || isset($request->video_code) || isset($request->video_source))) {
                    throw new \Exception();
                }

                $proposal->fill([
                    'detalle' => $request->has('detalle') ? $request->detalle : $proposal->detalle,
                    'type' => $request->has('type') ? $request->type : $proposal->type,
                    'state' => $state,
                    'is_public' => $request->has('is_public') ? $request->is_public : $proposal->is_public,
                    'argument' => $argument,
                    'video_code' => $videoCode,
                    'video_source' => $videoSource
                ]);
            } else {
                $proposal = Proposal::where([
                    ['id', $id],
                    ['user_id', Auth::id()]
                ])->firstOrFail();

                $argument = $proposal->argument;
                $videoCode = $proposal->video_code;
                $videoSource = $proposal->video_source;
                if($proposal->state == 1) {
                    if(isset($request->argument)) {
                        $argument = $request->argument;
                    }
                    if(isset($request->video_code)) {
                        $videoCode = $request->video_code;
                    }
                    if(isset($request->video_source)) {
                        $videoSource = $request->video_source;
                    }
                } else if($proposal->state == 0 && (isset($request->argument) || isset($request->video_code) || isset($request->video_source))) {
                    throw new \Exception();
                }

                $proposal->fill([
                    'detalle' => $request->has('detalle') ? $request->detalle : $proposal->detalle,
                    'argument' => $argument,
                    'video_code' => $videoCode,
                    'video_source' => $videoSource
                ]);
            }

            $proposal->save();
            return response()->json([
                'message' => 'Successfully updated proposal!',
                'data' => $proposal->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the proposal was not updated.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
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

            if(Auth::user()->hasRole('ADMIN')) {
                $proposal = Proposal::withTrashed()->findOrFail($id);
            } else {
                $proposal = Proposal::withTrashed()
                    ->where([
                        ['id', $id],
                        ['user_id', Auth::id()]
                    ])->firstOrFail();
            }
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $proposal->forceDelete();
            } else {
                $proposal->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted proposal!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the proposal was not deleted.'], 412);
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
            Proposal::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted proposal!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the proposal was not undeleted.'], 412);
        }
    }

    /**
     * Display the user associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function user($id)
    {
        try {
            $proposal = Proposal::findOrFail($id);

            if(!$proposal->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($proposal->user, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the proposal was not found.'], 412);
        }
    }

    /**
     * Display the urgencies.
     * About access control: all users can use this method (see routes).
     *
     *  @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function urgencies(Request $request, $id)
    {
        try {
            $proposal = Proposal::findOrFail($id);

            if(!$proposal->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $request['proposal_id'] = $proposal->id;
            $urgencyController = new UrgencyController();

            return response()->json($urgencyController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the proposal was not found.'], 412);
        }
    }

    /**
     * Store a newly created resource (urgency associated with a proposal) in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function storeUrgency(Request $request, $id)
    {
        try {
            $request['user_id'] = Auth::id();
            $validator = Validator::make($request->all(), [
                'urgency' => 'required|integer|in:1,2',
                'user_id' => 'required|integer|unique:urgencies,user_id,NULL,NULL,proposal_id,' . $id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $proposal = Proposal::findOrFail($id);
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
                'message' => 'Successfully created urgency of proposal!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the urgency of proposal was not created.'], 412);
        }
    }

    /**
     * Display the specified resource (user urgency associated with a proposal) in storage.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function showUserUrgency(Request $request, $id)
    {
        try {
            $urgency = Urgency::where([
                ['proposal_id', $id],
                ['user_id', $request->user_id]
            ]);

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                $urgency = $urgency->join('proposals', 'urgencies.proposal_id', '=', 'proposals.id')
                    ->select('urgencies.*')
                    ->where('proposals.is_public', true);
            }
            $urgency = $urgency->firstOrFail();

            return response()->json($urgency, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user urgency associated of proposal was not found.'], 412);
        }
    }
}
