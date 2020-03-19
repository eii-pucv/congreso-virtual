<?php

namespace App\Http\Controllers;

use App\Idea;
use App\Comment;
use App\Position;
use App\Project;
use App\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IdeaController extends Controller
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
            if($request->has('project_id')) {
                $filter['project'] = $request->project_id;
            }

            if(Auth::check() && Auth::user()->hasRole('ADMIN')) {
                $filter['projectIsPublic'] = $request->query('is_public', null);
                if(isset($request->only_trashed) && $request->only_trashed) {
                    $ideas = Idea::filter($filter)->onlyTrashed();
                } else {
                    $ideas = Idea::filter($filter);
                }
            } else {
                $filter['projectIsPublic'] = true;
                $ideas = Idea::filter($filter);
            }

            $totalResults = $ideas->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $ideas = $ideas->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $ideas = $ideas
                ->offset($offset)
                ->limit($limit);
            $ideas = $ideas->with(['project'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($ideas),
                'results' => $ideas
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: ideas were not found.'], 412);
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
                'titulo' => 'required|string|max:191',
                'detalle' => 'required|string',
                'project_id' => 'required|integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $project = Project::findOrFail($request->project_id);

            $idea = new Idea([
                'titulo' => $request->titulo,
                'detalle' => $request->detalle
            ]);
            $idea->project()->associate($project);
            $idea->save();
            return response()->json([
                'message' => 'Successfully created idea!',
                'data' => $idea->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the idea was not created.'], 412);
        }
    }

    /**
     * Display the specified resource.
     * About access control: all users can use this method (see routes).
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $idea = Idea::with(['project', 'votes'])->findOrFail($id);

            if(!$idea->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($idea, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the idea was not found.'], 412);
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
                'titulo' => 'required|string|max:191',
                'detalle' => 'required|string'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $idea = Idea::findOrFail($id);
            $idea->fill([
                'titulo' => $request->titulo,
                'detalle' => $request->detalle
            ]);
            $idea->save();
            return response()->json([
                'message' => 'Successfully updated idea!',
                'data' => $idea->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the idea was not updated.'], 412);
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

            $idea = Idea::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $idea->forceDelete();
            } else {
                $idea->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted idea!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the idea was not deleted.'], 412);
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
            Idea::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted idea!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the idea was not undeleted.'], 412);
        }
    }

    /**
     * Display the project associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function project($id)
    {
        try {
            $idea = Idea::findOrFail($id);

            if(!$idea->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($idea->project, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the idea was not found.'], 412);
        }
    }

    /**
     * Display the comments.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function comments(Request $request, $id)
    {
        try {
            $idea = Idea::findOrFail($id);

            if(!$idea->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $request['idea_id'] = $idea->id;
            unset($request['project_id'], $request['article_id'], $request['public_consultation_id']);
            $commentController = new CommentController();

            return response()->json($commentController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the idea was not found.'], 412);
        }
    }

    /**
     * Display the votes.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function votes(Request $request, $id)
    {
        try {
            $idea = Idea::findOrFail($id);

            if(!$idea->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $request['idea_id'] = $idea->id;
            unset($request['project_id'], $request['article_id'], $request['public_consultation_id']);
            $voteController = new VoteController();

            return response()->json($voteController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the idea was not found.'], 412);
        }
    }

    /**
     * Store a newly created resource (comment associated with a idea) in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function storeComment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'body' => 'required|string',
                'parent_id' => 'integer|nullable',
                'position_latitude' => 'string|nullable',
                'position_longitude' => 'string|nullable'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $parent = null;
            if(isset($request->parent_id)) {
                $parent = Comment::findOrFail($request->parent_id);
            }

            $idea = Idea::findOrFail($id);
            $project = $idea->project;
            if(!Auth::user()->hasRole('ADMIN')) {
                if(!$project->is_public || !$project->is_enabled) {
                    throw new \Exception();
                }
            }

            $user = Auth::user();

            $comment = new Comment([
                'body' => $request->body
            ]);
            $comment->parent()->associate($parent);
            $comment->idea()->associate($idea);
            $comment->user()->associate($user);

            DB::beginTransaction();
            $comment->save();

            if(isset($request->position_latitude) && isset($request->position_longitude)) {
                $position = new Position([
                    'latitude' => $request->position_latitude,
                    'longitude' => $request->position_longitude
                ]);
                $position->comment()->associate($comment);
                $position->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully created comment of idea!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the comment of idea was not created.'], 412);
        }
    }

    /**
     * Store a newly created resource (vote associated with a idea) in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function storeVote(Request $request, $id)
    {
        try {
            $request['user_id'] = Auth::id();
            $validator = Validator::make($request->all(), [
                'vote' => 'required|integer',
                'user_id' => 'required|integer|unique:votes,user_id,NULL,NULL,idea_id,' . $id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $idea = Idea::findOrFail($id);
            $project = $idea->project;
            if(!Auth::user()->hasRole('ADMIN')) {
                if(!$project->is_public || !$project->is_enabled || $project->etapa == 3) {
                    throw new \Exception();
                }
            }

            $currentDatetime = date("Y-m-d H:i:s", time());
            if($currentDatetime < $project->fecha_inicio || $currentDatetime > $project->fecha_termino) {
                throw new \Exception();
            }

            $user = Auth::user();

            $vote = new Vote([
                'vote' => $request->vote
            ]);
            $vote->idea()->associate($idea);
            $vote->user()->associate($user);
            $vote->save();

            $data = $vote->refresh()->toArray();
            unset($data['user']);

            return response()->json([
                'message' => 'Successfully created vote of idea!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the vote of idea was not created.'], 412);
        }
    }

    /**
     * Display the specified resource (user vote associated with a idea) in storage.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function showUserVote(Request $request, $id)
    {
        try {
            $vote = Vote::where([
                ['idea_id', $id],
                ['user_id', $request->user_id]
            ]);

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                $vote = $vote->join('ideas', 'votes.idea_id', '=', 'ideas.id')
                    ->join('projects', 'ideas.project_id', '=', 'projects.id')
                    ->select('votes.*')
                    ->where('projects.is_public', true);
            }
            $vote = $vote->firstOrFail();

            return response()->json($vote, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user vote associated of idea was not found.'], 412);
        }
    }
}
