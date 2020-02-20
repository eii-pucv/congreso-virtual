<?php

namespace App\Http\Controllers;

use App\Article;
use App\Comment;
use App\Position;
use App\Project;
use App\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
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
            } else {
                $filter['projectIsPublic'] = true;
            }
            $articles = Article::filter($filter);
            $totalResults = Article::filter($filter)->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $articles = $articles->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $articles = $articles
                ->offset($offset)
                ->limit($limit);
            $articles = $articles->with(['project'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($articles),
                'results' => $articles
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: articles were not found.'], 412);
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

            $article = new Article([
                'titulo' => $request->titulo,
                'detalle' => $request->detalle
            ]);
            $article->project()->associate($project);
            $article->save();
            return response()->json([
                'message' => 'Successfully created article!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the article was not created.'], 412);
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
            $article = Article::with(['project', 'votes'])->findOrFail($id);

            if(!$article->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($article, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the article was not found.'], 412);
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

            $article = Article::findOrFail($id);
            $article->fill([
                'titulo' => $request->titulo,
                'detalle' => $request->detalle
            ]);
            $article->save();
            return response()->json([
                'message' => 'Successfully updated article!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the article was not updated.'], 412);
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

            $article = Article::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $article->forceDelete();
            } else {
                $article->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted article!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the article was not deleted.'], 412);
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
            Article::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted article!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the article was not undeleted.'], 412);
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
            $article = Article::findOrFail($id);

            if(!$article->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($article->project, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the article was not found.'], 412);
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
            $article = Article::findOrFail($id);

            if(!$article->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $request['article_id'] = $article->id;
            unset($request['project_id'], $request['idea_id'], $request['public_consultation_id']);
            $commentController = new CommentController();

            return response()->json($commentController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the article was not found.'], 412);
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
            $article = Article::findOrFail($id);

            if(!$article->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $request['article_id'] = $article->id;
            unset($request['project_id'], $request['idea_id'], $request['public_consultation_id']);
            $voteController = new VoteController();

            return response()->json($voteController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the article was not found.'], 412);
        }
    }

    /**
     * Store a newly created resource (comment associated with a article) in storage.
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
                'body' => 'required|string|max:1000',
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

            $article = Article::findOrFail($id);
            $project = $article->project;
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
            $comment->article()->associate($article);
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
                'message' => 'Successfully created comment of article!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the comment of article was not created.'], 412);
        }
    }

    /**
     * Store a newly created resource (vote associated with a article) in storage.
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
                'user_id' => 'required|integer|unique:votes,user_id,NULL,NULL,article_id,' . $id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $article = Article::findOrFail($id);
            $project = $article->project;
            if(!Auth::user()->hasRole('ADMIN')) {
                if(!$project->is_public || !$project->is_enabled || $project->etapa != 2) {
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
            $vote->article()->associate($article);
            $vote->user()->associate($user);
            $vote->save();

            $data = $vote->refresh()->toArray();
            unset($data['user']);

            return response()->json([
                'message' => 'Successfully created vote of article!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the vote of article was not created.'], 412);
        }
    }

    /**
     * Display the specified resource (user vote associated with a article) in storage.
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
                ['article_id', $id],
                ['user_id', $request->user_id]
            ]);

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                $vote = $vote->join('articles', 'votes.article_id', '=', 'articles.id')
                    ->join('projects', 'articles.project_id', '=', 'projects.id')
                    ->select('votes.*')
                    ->where('projects.is_public', true);
            }
            $vote = $vote->firstOrFail();

            return response()->json($vote, 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user vote associated of article was not found.'], 412);
        }
    }
}
